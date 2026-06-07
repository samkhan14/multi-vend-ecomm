<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin user
        $user = User::firstOrCreate([
            'email' => 'superadmin@example.com'
        ], [
            'name' => 'Diginotive',
            'password' => bcrypt('password'),
            'user_type' => 'adminpanel',
        ]);

        // Ensure super-admin user also has a vendor record
        if (Schema::hasTable('vendors')) {
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (! $vendor) {
                $baseSlug = Str::slug(($user->name ?: 'super-admin') . '-store');
                $storeSlug = $baseSlug;
                $counter = 1;

                while (Vendor::where('store_slug', $storeSlug)->exists()) {
                    $storeSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $payload = [
                    'user_id' => $user->id,
                    'store_name' => ($user->name ?: 'Super Admin') . ' Store',
                    'store_slug' => $storeSlug,
                    'status' => 1,
                ];

                if (Schema::hasColumn('vendors', 'vendor_type')) {
                    $payload['vendor_type'] = 'super_admin';
                }

                if (Schema::hasColumn('vendors', 'is_block')) {
                    $payload['is_block'] = false;
                }

                Vendor::create($payload);
            } else {
                $updateData = [];

                if (Schema::hasColumn('vendors', 'status') && (int) $vendor->status !== 1) {
                    $updateData['status'] = 1;
                }

                if (Schema::hasColumn('vendors', 'vendor_type') && $vendor->vendor_type !== 'super_admin') {
                    $updateData['vendor_type'] = 'super_admin';
                }

                if (Schema::hasColumn('vendors', 'is_block') && (bool) $vendor->is_block !== false) {
                    $updateData['is_block'] = false;
                }

                if (! empty($updateData)) {
                    $vendor->update($updateData);
                }
            }
        }

        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );
        $vendor = Role::firstOrCreate(
            ['name' => 'Vendor', 'guard_name' => 'web']
        );

        $permissions = Permission::where('guard_name', 'web')->get();

        $superAdmin->syncPermissions($permissions);
        $user->assignRole($superAdmin);
    }
}
