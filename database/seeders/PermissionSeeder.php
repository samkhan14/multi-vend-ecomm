<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.detail',
            'banners.view',
            'banners.create',
            'banners.edit',
            'banners.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'orders.view',
            'orders.detail',
            'orders.status',
            'orders.delete',
            'brands.view',
            'brands.create',
            'brands.edit',
            'brands.delete',
            'attributes.view',
            'attributes.create',
            'attributes.edit',
            'attributes.delete',
            'variants.view',
            'variants.create',
            'variants.edit',
            'variants.delete',
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'subscribers.view',
            'subscribers.delete',
            'inquiries.view',
            'inquiries.delete',
            'ratings.view',
            'ratings.delete',
            'ratings.detail',
            'announcements.view',
            'announcements.create',
            'announcements.edit',
            'announcements.delete',
            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.delete',
            'seo.view',
            'seo.create',
            'seo.edit',
            'seo.delete',
            'site.view',
            'general.view',
            'shipping.view',
            'payment_gateways.view',
            'profile.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

    }
}
