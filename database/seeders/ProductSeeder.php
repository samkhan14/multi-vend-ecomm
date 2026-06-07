<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private const PRODUCTS_COUNT = 40;

    public function run(): void
    {
        if (! Schema::hasTable('products')) {
            $this->command?->warn('Products table does not exist. Skipping ProductSeeder.');
            return;
        }

        $categories = DB::table('categories')
            ->select('id', 'category_name')
            ->where('status', 1)
            ->get();

        $brands = DB::table('brands')
            ->select('id', 'name')
            ->where('status', 1)
            ->get();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command?->warn('Categories or brands missing. Run CategorySeeder and BrandSeeder first.');
            return;
        }

        $columns = collect(Schema::getColumnListing('products'));
        $hasVendorId = $columns->contains('vendor_id');
        $hasProductCode = $columns->contains('product_code');
        $hasProductColor = $columns->contains('product_color');

        $vendorIds = [];
        if ($hasVendorId) {
            if (! Schema::hasTable('vendors')) {
                $this->command?->warn('vendors table missing while products.vendor_id exists. Skipping ProductSeeder.');
                return;
            }

            $vendorQuery = DB::table('vendors')->select('id');

            if (Schema::hasColumn('vendors', 'vendor_type')) {
                $vendorQuery->where('vendor_type', 'vendor');
            }

            $vendorIds = $vendorQuery->pluck('id')->all();

            if (empty($vendorIds)) {
                $vendorIds = DB::table('vendors')->pluck('id')->all();
            }

            if (empty($vendorIds)) {
                $this->command?->warn('No vendors found. Run VendorUserSeeder first.');
                return;
            }
        }

        for ($i = 1; $i <= self::PRODUCTS_COUNT; $i++) {
            $category = $categories->random();
            $brand = $brands->random();
            $vendorId = $hasVendorId ? (int) $vendorIds[array_rand($vendorIds)] : null;

            $baseName = $this->makeProductName($category->category_name, $brand->name, $i);
            $baseSlug = Str::slug($baseName);
            $slug = $this->resolveUniqueSlug($baseSlug, $hasVendorId, $vendorId);

            $price = (float) (random_int(1500, 20000) / 100);
            $discountPercent = (float) random_int(0, 35);
            $stock = random_int(0, 120);

            $payload = [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'product_name' => $baseName,
                'product_slug' => $slug,
                'product_price' => $price,
                'product_discount' => $discountPercent,
                'product_weight' => random_int(200, 5000),
                'thumbnail_image' => 'products/thumbnails/' . $slug . '.jpg',
                'short_description' => "{$brand->name} {$category->category_name} collection - quality product.",
                'long_description' => "Seeded product for {$category->category_name} with brand {$brand->name}.",
                'stock' => $stock,
                'stock_status' => $stock > 0 ? 'in_stock' : 'out_of_stock',
                'is_featured' => random_int(0, 1),
                'order_by' => $i,
                'meta_title' => $baseName,
                'meta_keywords' => Str::lower($brand->name . ', ' . $category->category_name . ', ecommerce'),
                'meta_description' => "{$baseName} available with trusted quality and fast delivery.",
                'status' => 1,
                'updated_at' => now(),
            ];

            if ($hasVendorId) {
                $payload['vendor_id'] = $vendorId;
            }

            if ($hasProductCode) {
                $payload['product_code'] = $this->generateUniqueProductCode();
            }

            if ($hasProductColor) {
                $payload['product_color'] = collect([
                    'Black',
                    'White',
                    'Blue',
                    'Red',
                    'Green',
                    'Gray',
                ])->random();
            }

            $where = ['product_slug' => $slug];
            if ($hasVendorId) {
                $where['vendor_id'] = $vendorId;
            }

            DB::table('products')->updateOrInsert(
                $where,
                $payload + ['created_at' => now()]
            );
        }

        $this->command?->info('Products seeded using existing brand/category IDs.');
    }

    private function makeProductName(string $categoryName, string $brandName, int $index): string
    {
        $adjectives = ['Premium', 'Classic', 'Smart', 'Ultra', 'Pro', 'Essential', 'Modern', 'Elite'];

        return $brandName . ' ' . $adjectives[array_rand($adjectives)] . ' ' . $categoryName . ' ' . $index;
    }

    private function resolveUniqueSlug(string $baseSlug, bool $hasVendorId, ?int $vendorId): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $hasVendorId, $vendorId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, bool $hasVendorId, ?int $vendorId): bool
    {
        $query = DB::table('products')->where('product_slug', $slug);

        if ($hasVendorId) {
            if ($vendorId === null) {
                $query->whereNull('vendor_id');
            } else {
                $query->where('vendor_id', $vendorId);
            }
        }

        return $query->exists();
    }

    private function generateUniqueProductCode(): string
    {
        do {
            $code = 'PRD-' . strtoupper(Str::random(8));
        } while (DB::table('products')->where('product_code', $code)->exists());

        return $code;
    }
}
