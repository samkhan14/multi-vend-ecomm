<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
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
     * Order Item list access
     */
    public function viewAny(User $user): bool
    {
        return $user->can('orders.view.all') || $user->hasRole('Vendor');
    }

    /**
     * Single order item view
     */
    public function view(User $user, OrderItem $orderItem): bool
    {
        // Admin / Manager permission
        if ($user->can('orders.view.all')) {
            return true;
        }

        // Vendor ownership
        return $user->vendor
            && $orderItem->vendor_id === $user->vendor->id;
    }

    /**
     * Update order item (status change etc.)
     */
    public function update(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('orders.update.all')) {
            return true;
        }

        return $user->vendor
            && $orderItem->vendor_id === $user->vendor->id;
    }

    /**
     * Delete order item
     */
    public function delete(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('orders.delete.all')) {
            return true;
        }

        return $user->vendor
            && $orderItem->vendor_id === $user->vendor->id;
    }
}
