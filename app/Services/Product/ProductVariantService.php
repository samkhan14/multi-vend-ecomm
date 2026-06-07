<?php

namespace App\Services\Product;

use App\Models\ProductVariant;
use App\Models\ProductVariantValue;

class ProductVariantService
{
    protected ProductImageService $imageService;

    public function __construct(ProductImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function updateVariantsFromCombinations($product, array $combinations): void
    {
        // Get all existing variant IDs from combinations
        $existingVariantIds = collect($combinations)
            ->pluck('variant_id')
            ->filter()
            ->toArray();

        // Delete variants that are no longer in the form
        \App\Models\ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', $existingVariantIds)
            ->each(function ($variant) {
                // Delete variant images from storage
                foreach ($variant->images as $img) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image);
                    $img->delete();
                }
                // Delete variant values
                $variant->VariantValues()->delete();
                // Delete variant
                $variant->delete();
            });

        // Update or create variants
        foreach ($combinations as $combination) {
            
            // ===== FIX: Check if sale_price is empty string =====
            $salePrice = null;
            if (isset($combination['sale_price']) && $combination['sale_price'] !== '' && $combination['sale_price'] !== null) {
                $salePrice = $combination['sale_price'];
            }
            
            // ===== FIX: Check weight =====
            $weight = null;
            if (isset($combination['weight']) && $combination['weight'] !== '' && $combination['weight'] !== null) {
                $weight = $combination['weight'];
            }
            
            if (! empty($combination['variant_id'])) {
                // Update existing variant
                $productVariant = \App\Models\ProductVariant::find($combination['variant_id']);
                if ($productVariant) {
                    $productVariant->update([
                        'sku' => $combination['sku'],
                        'barcode' => $combination['barcode'] ?? null,
                        'price' => $combination['price'],
                        'sale_price' => $salePrice, // FIXED: Use checked value
                        'stock' => $combination['stock'] ?? 0,
                        'variant_slug' => $combination['slug'],
                        'combination_label' => $combination['label'],
                        'weight' => $weight, // FIXED: Use checked value
                        'status' => $combination['status'] ? 1 : 0,
                    ]);

                    // Handle new variant images
                    if (! empty($combination['images'])) {
                        $this->imageService->storeVariantImages(
                            $productVariant,
                            $combination['images']
                        );
                    }
                }
            } else {
                // Create new variant
                $user = \Illuminate\Support\Facades\Auth::user();
                $vendorId = $user && $user->hasRole('Vendor')
                    ? $user->vendorId()
                    : admin_vendor_id();
                    
                $variant = \App\Models\ProductVariant::create([
                    'product_id'        => $product->id,
                    'vendor_id'         => $vendorId,
                    'sku'               => $combination['sku'],
                    'barcode'           => $combination['barcode'] ?? null,
                    'price'             => $combination['price'],
                    'sale_price'        => $salePrice, // FIXED: Use checked value
                    'stock'             => $combination['stock'] ?? 0,
                    'variant_slug'      => $combination['slug'],
                    'combination_label' => $combination['label'],
                    'weight' => $weight, // FIXED: Use checked value
                    'status'            => $combination['status'] ? 1 : 0,
                ]);

                // Save variant values for new variant
                foreach ($combination['combination'] as $variantId => $valueId) {
                    \App\Models\ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'variant_id'         => $variantId,
                        'variant_value_id'   => $valueId,
                    ]);
                }

                // Handle variant images for new variant
                if (! empty($combination['images'])) {
                    $this->imageService->storeVariantImages(
                        $variant,
                        $combination['images']
                    );
                }
            }
        }
    }

    public function createVariantsFromCombinations($product, array $combinations): void
    {
        foreach ($combinations as $combo) {

            $user = \Illuminate\Support\Facades\Auth::user();
            $vendorId = $user && $user->hasRole('Vendor')
                ? $user->vendorId()
                : admin_vendor_id();

            // ===== FIX: Check if sale_price is empty string =====
            $salePrice = null;
            if (isset($combo['sale_price']) && $combo['sale_price'] !== '' && $combo['sale_price'] !== null) {
                $salePrice = $combo['sale_price'];
            }
            
            // ===== FIX: Check weight =====
            $weight = null;
            if (isset($combo['weight']) && $combo['weight'] !== '' && $combo['weight'] !== null) {
                $weight = $combo['weight'];
            }

            $variant = ProductVariant::create([
                'product_id'        => $product->id,
                'vendor_id'         => $vendorId,
                'sku'               => $combo['sku'],
                'barcode'           => $combo['barcode'] ?? null,
                'price'             => $combo['price'],
                'sale_price'        => $salePrice, // FIXED: Use checked value
                'stock'             => $combo['stock'] ?? 0,
                'variant_slug'      => $combo['slug'],
                'combination_label' => $combo['label'],
                'weight' => $weight, // FIXED: Use checked value
                'status'            => $combo['status'] ? 1 : 0,
            ]);

            foreach ($combo['combination'] as $variantId => $valueId) {
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'variant_id'         => $variantId,
                    'variant_value_id'   => $valueId,
                ]);
            }

            if (! empty($combo['images'])) {
                $this->imageService->storeVariantImages(
                    $variant,
                    $combo['images']
                );
            }
        }
    }
}