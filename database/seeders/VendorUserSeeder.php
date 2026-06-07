<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class VendorUserSeeder extends Seeder
{
    private const MIN_USERS = 15;
    private const MAX_USERS = 20;

    public function run(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('vendors')) {
            $this->command?->warn('Users or vendors table missing. Skipping VendorUserSeeder.');
            return;
        }

        $vendorRole = Role::query()
            ->where('name', 'Vendor')
            ->where('guard_name', 'web')
            ->first();

        $vendorsToCreate = random_int(self::MIN_USERS, self::MAX_USERS);

        for ($i = 1; $i <= $vendorsToCreate; $i++) {
            $email = "vendor{$i}@example.com";

            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Vendor User {$i}",
                    'password' => Hash::make('password'),
                    'user_type' => 'vendorpanel',
                    'user_status' => 1,
                    'email_verified_at' => now(),
                ]
            );

            if ($vendorRole && ! $user->hasRole('Vendor')) {
                $user->assignRole($vendorRole);
            }

            $baseSlug = Str::slug("vendor-store-{$i}");
            $storeSlug = $this->resolveUniqueStoreSlug($baseSlug, $user->id);

            $payload = [
                'user_id' => $user->id,
                'store_name' => "Vendor Store {$i}",
                'store_slug' => $storeSlug,
                'business_type' => 'General',
                'phone' => '+92 300 ' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => "Street {$i}, Karachi",
                'city' => 'Karachi',
                'country' => 'Pakistan',
                'status' => 1,
            ];

            if (Schema::hasColumn('vendors', 'vendor_type')) {
                $payload['vendor_type'] = 'vendor';
            }

            if (Schema::hasColumn('vendors', 'is_block')) {
                $payload['is_block'] = 0;
            }

            if (Schema::hasColumn('vendors', 'created_at')) {
                $payload['created_at'] = now();
            }
            if (Schema::hasColumn('vendors', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            Vendor::query()->updateOrCreate(
                ['user_id' => $user->id],
                $payload
            );
        }

        $this->command?->info("Created/updated {$vendorsToCreate} vendor users with vendor profiles.");
    }

    private function resolveUniqueStoreSlug(string $baseSlug, int $userId): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (
            Vendor::query()
                ->where('store_slug', $slug)
                ->where('user_id', '!=', $userId)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
