<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    private const PLACEHOLDER_PNG_BASE64 =
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0p0YQAAAAASUVORK5CYII=';

    public function run(): void
    {
        // Disable foreign key checks
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Brand::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $brands = [
            'Nike',
            'Adidas',
            'Puma',
            'Reebok',
            'Under Armour',
            'New Balance',
            'Asics',
            'Converse',
            'Vans',
            'Fila',
            'Skechers',
            'Jordan',
            'Timberland',
            'Columbia',
            'The North Face',
            'Patagonia',
            "Levi's",
            'Wrangler',
            'Lee',
            'Diesel'
        ];

        // Ensure directory exists
        $path = storage_path('app/public/brands');
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        foreach ($brands as $name) {
            $slug = Str::slug($name);
            $imageName = $slug . '.png';
            $imagePath = $path . '/' . $imageName;

            if (! file_exists($imagePath)) {
                // Keep seed fast and deterministic (no remote HTTP calls).
                file_put_contents($imagePath, base64_decode(self::PLACEHOLDER_PNG_BASE64));
            }

            Brand::create([
                'name' => $name,
                'slug' => $slug,
                'image' => 'brands/' . $imageName,
                'description' => "Official authentic products from {$name}. High quality and durable.",
                'status' => 1,
            ]);
        }

        $this->command?->info('All brands created with local placeholder images.');
    }
}
