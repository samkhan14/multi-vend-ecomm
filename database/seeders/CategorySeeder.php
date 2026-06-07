<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    private const PLACEHOLDER_PNG_BASE64 =
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0p0YQAAAAASUVORK5CYII=';

    public function run(): void
    {
        // Disable foreign key checks
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Category::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Ensure directory exists
        $path = storage_path('app/public/categories');
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $categories = [
            [
                'name' => 'Electronics',
                'children' => [
                    [
                        'name' => 'Computers & Laptops',
                        'children' => [
                            ['name' => 'Gaming Laptops'],
                            ['name' => 'Ultrabooks'],
                            ['name' => 'MacBooks'],
                            ['name' => 'Accessories'],
                        ]
                    ],
                    [
                        'name' => 'Smartphones & Tablets',
                        'children' => [
                            ['name' => 'Apple iPhone'],
                            ['name' => 'Samsung Galaxy'],
                            ['name' => 'Tablets'],
                            ['name' => 'Phone Accessories'],
                        ]
                    ],
                    [
                        'name' => 'Cameras & Audio',
                        'children' => [
                            ['name' => 'DSLR Cameras'],
                            ['name' => 'Headphones'],
                            ['name' => 'Bluetooth Speakers'],
                            ['name' => 'Home Audio'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Fashion',
                'children' => [
                    [
                        'name' => 'Men Fashion',
                        'children' => [
                            ['name' => 'T-Shirts & Polos'],
                            ['name' => 'Jeans & Trousers'],
                            ['name' => 'Formal Shirts'],
                            ['name' => 'Shoes & Sneakers'],
                        ]
                    ],
                    [
                        'name' => 'Women Fashion',
                        'children' => [
                            ['name' => 'Dresses & Skirts'],
                            ['name' => 'Tops & Tees'],
                            ['name' => 'Handbags & Wallets'],
                            ['name' => 'Heels & Sandals'],
                        ]
                    ],
                    [
                        'name' => 'Watches & Jewelry',
                        'children' => [
                            ['name' => 'Men Watches'],
                            ['name' => 'Women Watches'],
                            ['name' => 'Necklaces'],
                            ['name' => 'Rings & Earrings'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Home & Living',
                'children' => [
                    [
                        'name' => 'Furniture',
                        'children' => [
                            ['name' => 'Living Room'],
                            ['name' => 'Bedroom'],
                            ['name' => 'Office Furniture'],
                            ['name' => 'Outdoor'],
                        ]
                    ],
                    [
                        'name' => 'Kitchen & Dining',
                        'children' => [
                            ['name' => 'Cookware'],
                            ['name' => 'Tableware'],
                            ['name' => 'Kitchen Appliances'],
                            ['name' => 'Coffee & Tea'],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($categories as $rootCat) {
            // Root category: Level 0, Parent ID null
            $this->createCategory($rootCat, null, 0);
        }

        $this->command?->info('All categories created with 3-level hierarchy (0-1-2).');
    }

    private function createCategory(array $data, $parentId, int $level): void
    {
        $name = $data['name'];
        $slug = Str::slug($name);
        $imageName = $slug . '.png';
        $path = storage_path('app/public/categories');
        $imagePath = $path . '/' . $imageName;

        if (! file_exists($imagePath)) {
            // Keep seed fast and deterministic (no remote HTTP calls).
            file_put_contents($imagePath, base64_decode(self::PLACEHOLDER_PNG_BASE64));
        }

        $category = Category::create([
            'parent_id' => $parentId,
            'level' => $level,
            'category_name' => $name,
            'category_image' => 'categories/' . $imageName,
            'url' => $slug,
            'description' => "Best collection of {$name}",
            'status' => 1,
            'category_discount' => 0,
        ]);

        if (isset($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createCategory($child, $category->id, $level + 1);
            }
        }
    }
}
