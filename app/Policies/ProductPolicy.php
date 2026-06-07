<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Super Admin global override
     */
    public function before(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    /**
     * Product list access
     */
    public function viewAny(User $user): bool
    {
        return $user->can('products.view.all')
            || $user->hasRole('Vendor');
    }

    /**
     * Single product view
     */

    public function view(User $user, Product $product): bool
    {
        // Admin / Manager via permission
        if ($user->can('products.view.all')) {
            return true;
        }

        // Vendor ownership
        return $product->vendor_id === vendor_or_admin_id();
    }

    /**
     * Create product
     */
    
    public function create(User $user): bool
    {
        return $user->can('products.create')
            || $user->hasRole('Vendor');
    }

    /**
     * Update product
     */
    public function update(User $user, Product $product): bool
    {
        // Admin / Manager
        if ($user->can('products.update.all')) {
            return true;
        }

        // Vendor ownership
        return $product->vendor_id === vendor_or_admin_id();
    }

    /**
     * Delete product
     */
    public function delete(User $user, Product $product): bool
    {
        // Admin / Manager
        if ($user->can('products.delete.all')) {
            return true;
        }

        // Vendor ownership
        return $product->vendor_id === vendor_or_admin_id();
    }
}
