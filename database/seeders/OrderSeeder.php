<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    private const DEFAULT_ORDERS_COUNT = 6;
    private const DEFAULT_TAX_PERCENT = 5.0;

    public function run(): void
    {
        $user = User::query()->first();

        $hasProductVendorId = Schema::hasTable('products') && Schema::hasColumn('products', 'vendor_id');

        $productQuery = Product::query()
            ->where('status', 1)
            ->with(['productVariants.variantValues.variant', 'productVariants.variantValues.variantValue']);

        if ($hasProductVendorId) {
            $productQuery->whereNotNull('vendor_id');
        }

        $products = $productQuery->get();

        if ($products->isEmpty()) {
            $this->command?->error('No active vendor products found. Seed vendor products first, then run OrderSeeder.');
            return;
        }

        $commissionPercent = $this->normalizePercent((float) (GeneralSetting::query()->value('commission') ?? 0));

        for ($i = 0; $i < self::DEFAULT_ORDERS_COUNT; $i++) {
            $this->createOrderWithItems($products, $user, $commissionPercent);
        }

        $this->command?->info('OrderSeeder prepared realistic orders with full item-level calculations.');
    }

    private function createOrderWithItems(Collection $products, ?User $user, float $commissionPercent): void
    {
        $orderProducts = $this->pickProductsForOrder($products);
        $orderStatus = $this->randomOrderStatus();
        $createdAt = now()->subDays(random_int(0, 30));
        $shippingCharges = (float) (random_int(0, 1) ? 150 : 0);
        $couponPercent = random_int(0, 100) <= 30 ? (float) random_int(5, 15) : 0.0;

        DB::transaction(function () use ($orderProducts, $user, $commissionPercent, $orderStatus, $createdAt, $shippingCharges, $couponPercent): void {
            $order = Order::create([
                'user_id' => $user?->id,
                'session_id' => 'seed-session-' . uniqid(),
                'name' => $user?->name ?? 'Guest Customer',
                'email' => $user?->email ?? 'guest@example.com',
                'mobile' => '+92 300 1234567',
                'address' => 'House # ' . random_int(1, 999) . ', Street ' . random_int(1, 99) . ', Block ' . chr(random_int(65, 90)),
                'city' => 'Karachi',
                'state' => 'Sindh',
                'country' => 'Pakistan',
                'pincode' => '75500',
                'subtotal' => 0,
                'shipping_charges' => 0,
                'tax_amount' => 0,
                'coupon_amount' => 0,
                'grand_total' => 0,
                'payment_method' => 'Cash on Delivery',
                'payment_status' => in_array($orderStatus, ['completed', 'delivered'], true) ? 'paid' : 'unpaid',
                'status' => $orderStatus,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $taxableTotal = 0.0;
            $taxTotal = 0.0;
            $itemsSubtotalTotal = 0.0;
            $itemsCount = 0;

            foreach ($orderProducts as $product) {
                $variant = $this->pickVariant($product);
                $vendorId = $this->resolveVendorId($product, $variant);

                if ($vendorId === null) {
                    continue;
                }

                $unitPrice = $this->resolveUnitPrice($product, $variant);

                if ($unitPrice <= 0) {
                    continue;
                }

                $quantity = $this->resolveQuantity($product, $variant);
                $discountPercent = $this->normalizePercent((float) ($product->product_discount ?? 0));

                $lineBase = round($unitPrice * $quantity, 2);
                $lineDiscount = round(($lineBase * $discountPercent) / 100, 2);
                $lineTaxable = round(max($lineBase - $lineDiscount, 0), 2);
                $lineTax = round(($lineTaxable * self::DEFAULT_TAX_PERCENT) / 100, 2);
                $lineSubtotal = round($lineTaxable + $lineTax, 2);
                $lineCommission = round(($lineSubtotal * $commissionPercent) / 100, 2);
                $lineFinalPrice = round(max($lineSubtotal - $lineCommission, 0), 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'vendor_id' => $vendorId,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->product_name,
                    'product_sku' => $variant?->sku ?: ($product->product_code ?? ('PRD-' . $product->id)),
                    'variant_name' => $variant?->combination_label,
                    'variant_attributes' => $this->buildVariantAttributes($variant),
                    'price' => round($unitPrice, 2),
                    'quantity' => $quantity,
                    'discount' => $lineDiscount,
                    'tax' => $lineTax,
                    'commission' => $lineCommission,
                    'final_price' => $lineFinalPrice,
                    'wallet_added' => false,
                    'subtotal' => $lineSubtotal,
                    'status' => $this->resolveItemStatus($orderStatus),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $itemsCount++;
                $taxableTotal += $lineTaxable;
                $taxTotal += $lineTax;
                $itemsSubtotalTotal += $lineSubtotal;
            }

            if ($itemsCount === 0) {
                $order->delete();
                return;
            }

            $couponAmount = round(($itemsSubtotalTotal * $couponPercent) / 100, 2);
            $grandTotal = round(max(($itemsSubtotalTotal + $shippingCharges) - $couponAmount, 0), 2);

            $order->update([
                'subtotal' => round($itemsSubtotalTotal, 2),
                'shipping_charges' => round($shippingCharges, 2),
                'tax_amount' => round($taxTotal, 2),
                'coupon_amount' => $couponAmount,
                'grand_total' => $grandTotal,
                'shipped_at' => in_array($orderStatus, ['processing', 'completed', 'delivered'], true) ? $createdAt->copy()->addDay() : null,
                'delivered_at' => in_array($orderStatus, ['completed', 'delivered'], true) ? $createdAt->copy()->addDays(2) : null,
                'notes' => sprintf(
                    'Seeded order with %.2f%% item tax and %.2f%% commission. Taxable total: %.2f',
                    self::DEFAULT_TAX_PERCENT,
                    $commissionPercent,
                    round($taxableTotal, 2)
                ),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status' => $orderStatus,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        });
    }

    private function pickProductsForOrder(Collection $products): Collection
    {
        $maxItems = min(5, $products->count());
        $take = random_int(1, max(1, $maxItems));

        $selected = $products->random($take);

        return $selected instanceof Collection ? $selected->values() : collect([$selected]);
    }

    private function pickVariant(Product $product): ?ProductVariant
    {
        $variants = $product->productVariants;

        if ($variants->isEmpty()) {
            return null;
        }

        $activeVariants = $variants->where('status', true)->values();

        if ($activeVariants->isEmpty()) {
            return null;
        }

        return $activeVariants->random();
    }

    private function resolveUnitPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant) {
            $salePrice = (float) ($variant->sale_price ?? 0);
            $variantPrice = (float) ($variant->price ?? 0);

            if ($salePrice > 0) {
                return $salePrice;
            }

            if ($variantPrice > 0) {
                return $variantPrice;
            }
        }

        return max((float) ($product->product_price ?? 0), 0);
    }

    private function resolveQuantity(Product $product, ?ProductVariant $variant): int
    {
        $availableStock = (int) ($variant?->stock ?? $product->stock ?? 0);

        if ($availableStock <= 0) {
            return 1;
        }

        return random_int(1, min(3, $availableStock));
    }

    private function resolveVendorId(Product $product, ?ProductVariant $variant): ?int
    {
        $productVendorId = (int) ($product->vendor_id ?? 0);
        if ($productVendorId > 0) {
            return $productVendorId;
        }

        $variantVendorId = (int) ($variant?->vendor_id ?? 0);
        if ($variantVendorId > 0) {
            return $variantVendorId;
        }

        return null;
    }

    private function buildVariantAttributes(?ProductVariant $variant): ?array
    {
        if (! $variant) {
            return null;
        }

        $attributes = [];

        foreach ($variant->variantValues as $variantValue) {
            $name = $variantValue->variant?->name;
            $value = $variantValue->variantValue?->value;

            if ($name && $value) {
                $attributes[$name] = $value;
            }
        }

        return empty($attributes) ? null : $attributes;
    }

    private function normalizePercent(float $value): float
    {
        return max(0, min(100, $value));
    }

    private function randomOrderStatus(): string
    {
        $statuses = ['pending', 'processing', 'completed', 'delivered', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }

    private function resolveItemStatus(string $orderStatus): string
    {
        return match ($orderStatus) {
            'cancelled' => 'cancelled',
            'completed', 'delivered' => ['completed', 'delivered'][array_rand(['completed', 'delivered'])],
            'processing' => ['pending', 'processing'][array_rand(['pending', 'processing'])],
            default => 'pending',
        };
    }
}
