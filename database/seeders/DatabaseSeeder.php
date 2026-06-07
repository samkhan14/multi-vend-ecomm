<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            VendorUserSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            // Intentionally disabled. Run manually only when requested.
            // OrderSeeder::class,
        ]);
    }
}
