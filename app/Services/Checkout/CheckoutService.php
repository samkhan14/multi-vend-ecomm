<?php

namespace App\Services\Checkout;

use App\DataTransferObjects\Payment\PaymentResult;
use App\Enums\PaymentGateway;
use App\Mail\OrderConfirmationMail;
use App\Models\frontend\Cart;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingCharges;
use App\Services\Payment\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CheckoutService
{
    public function __construct(
        private PaymentGatewayManager $paymentGatewayManager,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getCartData(): array
    {
        $sessionId = Session::getId();

        $query = Cart::with(['product', 'variant']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItems = $query->get();

        $count = 0;
        $total = 0;
        $items = [];

        foreach ($cartItems as $item) {
            $count += $item->quantity;
            $total += $item->price * $item->quantity;

            $items[] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'product_name' => $item->product->product_name,
                'variant_name' => $item->variant ? $item->variant->sku : null,
                'image' => $item->product->thumbnail_image ?? '',
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'subtotal' => (float) ($item->price * $item->quantity),
            ];
        }

        return [
            'count' => $count,
            'total' => $total,
            'items' => $items,
        ];
    }

    public function placeOrder(Request $request): PaymentResult
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'additional_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cod,nowpayments',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $paymentMethod = $request->string('payment_method')->toString();

        if (! $this->paymentGatewayManager->isEnabled($paymentMethod)) {
            throw new InvalidArgumentException('Selected payment method is not available.');
        }

        $sessionId = Session::getId();
        $userId = Auth::check() ? Auth::id() : null;

        if ($userId) {
            $cartItems = Cart::with(['product', 'variant'])
                ->where('user_id', $userId)
                ->get();
        } else {
            $cartItems = Cart::with(['product', 'variant'])
                ->where('session_id', $sessionId)
                ->get();
        }

        if ($cartItems->isEmpty()) {
            throw new InvalidArgumentException('Your cart is empty!');
        }

        $cartData = $this->calculateCartTotals($cartItems);
        $couponCode = Session::get('applied_coupon_code');
        $couponDiscount = Session::get('coupon_discount_amount', 0);
        $couponDiscountBase = Session::get('coupon_discount_amount_base', 0);
        $shippingFee = $this->calculateShipping($cartData['subtotal']);

        $userCurrency = getUserCurrency();
        $baseCurrency = getGeneralSetting()->currency ?? 'PKR';

        if ($userCurrency !== $baseCurrency) {
            $rate = getCurrencyRate($userCurrency, $baseCurrency);
            $shippingFeeBase = $shippingFee * $rate;
        } else {
            $shippingFeeBase = $shippingFee;
        }

        $conversionRate = getCurrencyRate($baseCurrency, $userCurrency);
        $grandTotal = $cartData['subtotal'] + $shippingFee - $couponDiscount;
        $grandTotalBase = $cartData['subtotal_base'] + $shippingFeeBase - $couponDiscountBase;
        $fullName = $request->first_name.' '.$request->last_name;

        DB::beginTransaction();

        try {
            $order = Order::query()->create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'name' => $fullName,
                'email' => $request->email,
                'mobile' => $request->phone,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->zip,
                'address' => $request->address,
                'subtotal' => $cartData['subtotal'],
                'shipping_charges' => $shippingFee,
                'tax_amount' => 0,
                'coupon_amount' => $couponDiscount,
                'coupon_code' => $couponCode,
                'grand_total' => $grandTotal,
                'order_currency' => $userCurrency,
                'conversion_rate' => $conversionRate,
                'base_amount' => $grandTotalBase,
                'payment_method' => $paymentMethod,
                'payment_gateway' => $paymentMethod,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'notes' => $request->additional_notes,
                'currency' => config('app.currency', '$'),
            ]);

            foreach ($cartItems as $cartItem) {
                $this->createOrderItem($order, $cartItem);
            }

            $gateway = $this->paymentGatewayManager->resolve($paymentMethod);
            $paymentResult = $gateway->initiate($order);

            if (! $paymentResult->success) {
                throw new InvalidArgumentException($paymentResult->message ?? 'Payment initiation failed.');
            }

            if ($paymentMethod === PaymentGateway::Cod->value) {
                try {
                    Mail::to($order->email)->send(new OrderConfirmationMail($order));
                } catch (\Exception $e) {
                    \Log::error('Email failed: '.$e->getMessage());
                }
            }

            $this->clearCartAndCoupon($userId, $sessionId);

            DB::commit();

            return $paymentResult;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function createOrderItem(Order $order, Cart $cartItem): void
    {
        $vendorId = $cartItem->product->vendor_id ?? null;
        $product = $cartItem->product;
        $basePrice = $product->product_price;

        if ($cartItem->product_variant_id && $cartItem->variant) {
            $basePrice = $cartItem->variant->price;
        }

        if ($product->sale_price && $product->sale_price > 0) {
            $currentDate = now();
            $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
            $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;

            $hasActiveSale = true;
            if ($saleStart && $currentDate < $saleStart) {
                $hasActiveSale = false;
            }
            if ($saleEnd && $currentDate > $saleEnd) {
                $hasActiveSale = false;
            }

            if ($hasActiveSale) {
                $basePrice = $product->sale_price;
            }
        }

        if ($product->product_discount && $product->product_discount > 0) {
            $basePrice = $product->product_price * (1 - $product->product_discount / 100);
        }

        $generalSetting = GeneralSetting::first();
        $commissionRate = (float) ($generalSetting->commission ?? 0);
        $commissionAmount = ($basePrice * $commissionRate) / 100;
        $finalPrice = $basePrice - $commissionAmount;

        $orderItem = OrderItem::query()->create([
            'order_id' => $order->id,
            'vendor_id' => $vendorId,
            'product_id' => $cartItem->product_id,
            'product_variant_id' => $cartItem->product_variant_id,
            'product_name' => $cartItem->product->product_name,
            'product_sku' => $cartItem->product->product_sku ?? null,
            'variant_name' => $cartItem->variant ? $cartItem->variant->sku : null,
            'variant_attributes' => $cartItem->variant ? json_encode($this->getVariantAttributes($cartItem->variant)) : null,
            'price' => $cartItem->price,
            'quantity' => $cartItem->quantity,
            'subtotal' => $cartItem->price * $cartItem->quantity,
            'base_price' => $basePrice,
            'base_subtotal' => $basePrice * $cartItem->quantity,
            'commission' => $commissionAmount,
            'final_price' => $finalPrice,
        ]);

        if ($cartItem->hasPrescription()) {
            $prescription = $cartItem->prescription;

            $orderItem->prescription()->create([
                'right_axis' => $prescription->right_axis,
                'right_spherical' => $prescription->right_spherical,
                'right_cylindrical' => $prescription->right_cylindrical,
                'left_axis' => $prescription->left_axis,
                'left_spherical' => $prescription->left_spherical,
                'left_cylindrical' => $prescription->left_cylindrical,
                'prescription_type' => $prescription->prescription_type ?? 'single_vision',
                'notes' => $prescription->notes,
                'prescription_image' => $prescription->prescription_image,
            ]);
        }

        $this->updateProductStock($cartItem);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Cart>  $cartItems
     * @return array{subtotal: float, subtotal_base: float}
     */
    private function calculateCartTotals($cartItems): array
    {
        $subtotal = 0;
        $subtotalBase = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item->price * $item->quantity;

            $product = $item->product;
            $basePrice = $product->product_price;

            if ($item->product_variant_id && $item->variant) {
                $basePrice = $item->variant->price;
            }

            if ($product->sale_price && $product->sale_price > 0) {
                $currentDate = now();
                $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
                $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;

                $hasActiveSale = true;
                if ($saleStart && $currentDate < $saleStart) {
                    $hasActiveSale = false;
                }
                if ($saleEnd && $currentDate > $saleEnd) {
                    $hasActiveSale = false;
                }

                if ($hasActiveSale) {
                    $basePrice = $product->sale_price;
                }
            }

            if ($product->product_discount && $product->product_discount > 0) {
                $basePrice = $product->product_price * (1 - $product->product_discount / 100);
            }

            $subtotalBase += $basePrice * $item->quantity;
        }

        return [
            'subtotal' => $subtotal,
            'subtotal_base' => $subtotalBase,
        ];
    }

    private function calculateShipping(float $cartTotalInUserCurrency): float
    {
        $shipping = ShippingCharges::where('status', 1)->first();

        if (! $shipping) {
            return 0;
        }

        $baseCurrency = getGeneralSetting()->currency ?? 'PKR';
        $userCurrency = getUserCurrency();

        $cartTotalInBase = $cartTotalInUserCurrency;
        if ($userCurrency !== $baseCurrency) {
            $rate = getCurrencyRate($userCurrency, $baseCurrency);
            $cartTotalInBase = $cartTotalInUserCurrency * $rate;
        }

        if ($shipping->max_order_amount && $cartTotalInBase >= $shipping->max_order_amount) {
            return 0;
        }

        if ($shipping->type == 'percentage') {
            $shippingFeeInBase = ($cartTotalInBase * $shipping->fee) / 100;
        } else {
            $shippingFeeInBase = $shipping->fee;
        }

        if ($userCurrency !== $baseCurrency) {
            $rateBack = getCurrencyRate($baseCurrency, $userCurrency);

            return $shippingFeeInBase * $rateBack;
        }

        return $shippingFeeInBase;
    }

    /**
     * @return array<string, mixed>
     */
    private function getVariantAttributes(ProductVariant $variant): array
    {
        $attributes = [];

        if ($variant->color) {
            $attributes['color'] = $variant->color;
        }
        if ($variant->size) {
            $attributes['size'] = $variant->size;
        }

        return $attributes;
    }

    private function updateProductStock(Cart $cartItem): void
    {
        if ($cartItem->product_variant_id) {
            $variant = ProductVariant::find($cartItem->product_variant_id);
            if ($variant) {
                $variant->stock = max(0, $variant->stock - $cartItem->quantity);
                $variant->save();
            }
        } else {
            $product = Product::find($cartItem->product_id);
            if ($product) {
                $product->stock = max(0, $product->stock - $cartItem->quantity);
                $product->stock_status = $product->stock > 0 ? 'in_stock' : 'out_of_stock';
                $product->save();
            }
        }
    }

    private function clearCartAndCoupon(?int $userId, string $sessionId): void
    {
        if ($userId) {
            Cart::where('user_id', $userId)->delete();
        } else {
            Cart::where('session_id', $sessionId)->delete();
        }

        Session::forget([
            'applied_coupon_code',
            'coupon_discount_amount',
            'coupon_discount_type',
            'coupon_minimum_amount',
            'coupon_discount_amount_base',
            'coupon_expiry',
        ]);
    }
}
