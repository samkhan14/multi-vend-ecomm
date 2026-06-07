<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\frontend\Cart;
use App\Models\ShippingCharges;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\GeneralSetting;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use Barryvdh\DomPDF\Facade\Pdf;

// ✅ YEH LINE ADD KARO - Helper functions import karne ke liye
if (!function_exists('getUserCurrency')) {
    require_once app_path('Helpers/FrontendHelper.php');
}
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function index()
    {
        // Get general settings
        $genralsetting = GeneralSetting::first();
        
        // Check if cart is empty
        $cartData = $this->getCartData();
        
        if ($cartData['count'] == 0) {
            return redirect()->route('cart.page')
                ->with('error', 'Your cart is empty!');
        }
        
        return view('frontend.checkout', compact('genralsetting'));
    }
    
 /**
 * Place order
 */
/**
 * Place order
 */
public function placeOrder(Request $request)
{
    DB::beginTransaction();
    
    try {
        // Validate form data
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
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Get cart items
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
            return redirect()->back()->with('error', 'Your cart is empty!');
        }
        
        // Calculate totals
        $cartData = $this->calculateCartTotals($cartItems);
        
        // Get coupon from session
        $couponCode = Session::get('applied_coupon_code');
        $couponDiscount = Session::get('coupon_discount_amount', 0);
        $couponDiscountBase = Session::get('coupon_discount_amount_base', 0);
        
        
// Calculate shipping
$shippingFee = $this->calculateShipping($cartData['subtotal']);

// Calculate base shipping amount for storing
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
$grandTotalBase = $cartData['subtotal_base'] + $shippingFeeBase - $couponDiscountBase; // ✅ CHANGE $couponDiscount to $couponDiscountBase
        
        // Create order
        $fullName = $request->first_name . ' ' . $request->last_name;
        
        $order = Order::create([
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
            'payment_method' => 'cod',
            'payment_gateway' => 'cod',
            'status' => 'pending',
            'notes' => $request->additional_notes,
            'currency' => config('app.currency', '$'),
        ]);
        
        // Create order items
        foreach ($cartItems as $cartItem) {
            $vendorId = $cartItem->product->vendor_id ?? null;
            
            // Calculate base price for this item
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
                if ($saleStart && $currentDate < $saleStart) $hasActiveSale = false;
                if ($saleEnd && $currentDate > $saleEnd) $hasActiveSale = false;
                
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


            $orderItem = OrderItem::create([
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
                  'commission' => $commissionAmount,     // ✅ ADD
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
        
        // Send email
        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
        }
        
        // Clear cart
        if ($userId) {
            Cart::where('user_id', $userId)->delete();
        } else {
            Cart::where('session_id', $sessionId)->delete();
        }
        
        // Clear coupon session
        Session::forget([
            'applied_coupon_code',
            'coupon_discount_amount',
            'coupon_discount_type',
            'coupon_minimum_amount',
            'coupon_discount_amount_base',
            'coupon_expiry'
        ]);
        
        DB::commit();
        
        return redirect()->route('checkout.thankyou', $order->order_number)
            ->with('success', 'Order placed successfully!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Order failed: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to place order: ' . $e->getMessage());
    }
}
    
    /**
     * Thank you page
     */
    public function thankyou($orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $genralsetting = GeneralSetting::first();
        
        return view('frontend.order.thankyou', compact('order', 'genralsetting'));
    }
    
    /**
     * Download invoice
     */
    public function downloadInvoice($orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $genralsetting = GeneralSetting::first();
        $siteSetting = SiteSetting::first();
        
        $pdf = PDF::loadView('frontend.order.invoice-pdf', compact('order', 'genralsetting', 'siteSetting'));
        $pdf->setPaper('A4');
        
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
    
    /**
     * API: Get checkout cart data
     */
    public function getCheckoutCart()
    {
        $cartData = $this->getCartData();
        
        return response()->json([
            'success' => true,
            'count' => $cartData['count'],
            'total' => $cartData['total'],
            'items' => $cartData['items']
        ]);
    }
    
    /**
     * Helper: Get cart data
     */
    private function getCartData()
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
            'items' => $items
        ];
    }
    
    /**
     * Helper: Calculate cart totals
     */
private function calculateCartTotals($cartItems)
{
    $subtotal = 0;        // User's currency (converted)
    $subtotalBase = 0;    // Base currency (admin's currency)
    
    foreach ($cartItems as $item) {
        // User's currency total (jo already cart mein store hai)
        $subtotal += $item->price * $item->quantity;
        
        // Base currency total calculate kar (product ki original price se)
        $product = $item->product;
        $basePrice = $product->product_price;
        
        // Agar variant hai to variant ki price lo
        if ($item->product_variant_id && $item->variant) {
            $basePrice = $item->variant->price;
        }
        
        // Agar sale price hai to wo lo
        if ($product->sale_price && $product->sale_price > 0) {
            $currentDate = now();
            $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
            $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;
            
            $hasActiveSale = true;
            if ($saleStart && $currentDate < $saleStart) $hasActiveSale = false;
            if ($saleEnd && $currentDate > $saleEnd) $hasActiveSale = false;
            
            if ($hasActiveSale) {
                $basePrice = $product->sale_price;
            }
        }
        
        // Agar discount hai to apply kar
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
    
    /**
     * Helper: Calculate shipping
     */
private function calculateShipping($cartTotalInUserCurrency)
{
    $shipping = ShippingCharges::where('status', 1)->first();
    
    if (!$shipping) {
        return 0;
    }
    
    // ✅ Convert cart total to base currency for threshold check
    $baseCurrency = getGeneralSetting()->currency ?? 'PKR';
    $userCurrency = getUserCurrency();
    
    $cartTotalInBase = $cartTotalInUserCurrency;
    if ($userCurrency !== $baseCurrency) {
        $rate = getCurrencyRate($userCurrency, $baseCurrency);
        $cartTotalInBase = $cartTotalInUserCurrency * $rate;
    }
    
    // ✅ Check threshold in base currency (PKR)
    if ($shipping->max_order_amount && $cartTotalInBase >= $shipping->max_order_amount) {
        return 0;
    }
    
    // ✅ Shipping fee bhi base currency mein calculate karo
    if ($shipping->type == 'percentage') {
        $shippingFeeInBase = ($cartTotalInBase * $shipping->fee) / 100;
    } else {
        $shippingFeeInBase = $shipping->fee;
    }
    
    // ✅ Convert shipping fee back to user currency
    if ($userCurrency !== $baseCurrency) {
        $rateBack = getCurrencyRate($baseCurrency, $userCurrency);
        $shippingFeeInUserCurrency = $shippingFeeInBase * $rateBack;
    } else {
        $shippingFeeInUserCurrency = $shippingFeeInBase;
    }
    
    return $shippingFeeInUserCurrency;
}
    
    /**
     * Helper: Get variant attributes
     */
    private function getVariantAttributes($variant)
    {
        $attributes = [];
        
        if ($variant->color) $attributes['color'] = $variant->color;
        if ($variant->size) $attributes['size'] = $variant->size;
        
        return $attributes;
    }
    
    /**
     * Helper: Update stock
     */
    private function updateProductStock($cartItem)
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
}