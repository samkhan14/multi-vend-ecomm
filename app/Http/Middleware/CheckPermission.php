<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $routeName = $request->route()->getName();
        
        if ($user) {
            // Check if user has Super Admin role
            if ($user->hasRole('Super Admin')) {
                return $next($request);
            }
        }

        // Get all permissions from database
        $allPermissions = Permission::pluck('name')->toArray();
        
        // Check if route name needs permission (only if permission exists in database)
        $requiredPermission = $this->getRequiredPermission($routeName);
        
        if (!empty($requiredPermission) && in_array($requiredPermission, $allPermissions)) {
            if (!$user || !$user->hasPermissionTo($requiredPermission)) {
                abort(403, 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }

    /**
     * Get required permission based on route name
     */ 
    private function getRequiredPermission($routeName): string
    {
        $permissionMap = [
            'admin.dashboard' => 'dashboard.view',
            'admin.user' => 'users.view',
            'admin.user.create' => 'users.create',
            'admin.user.edit' => 'users.edit',
            'admin.banner' => 'banners.view',
            'admin.banner.create' => 'banners.create',
            'admin.banner.edit' => 'banners.edit',
            'admin.brand' => 'brands.view',
            'admin.brand.create' => 'brands.create',
            'admin.brand.edit' => 'brands.edit',
            'admin.categories' => 'categories.view',
            'admin.categories.create' => 'categories.create',
            'admin.categories.edit' => 'categories.edit',
            'admin.attribute' => 'attributes.view',
            'admin.attribute.create' => 'attributes.create',
            'admin.attribute.edit' => 'attributes.edit',
            'admin.variant' => 'variants.view',
            'admin.variant.create' => 'variants.create',
            'admin.variant.edit' => 'variants.edit',
            'admin.product' => 'products.view',
            'admin.product.create' => 'products.create',
            'admin.product.edit' => 'products.edit',
            'admin.coupon' => 'coupons.view',
            'admin.coupon.create' => 'coupons.create',
            'admin.coupon.edit' => 'coupons.edit',
            'admin.rating' => 'ratings.view',
            'admin.rating.detail' => 'ratings.detail',
            'admin.orders' => 'orders.view',
            'admin.orders.detail' => 'orders.detail',
            'admin.orders.status' => 'orders.status',
            'admin.annoucement' => 'announcements.view',
            'admin.annoucement.create' => 'announcements.create',
            'admin.annoucement.edit' => 'announcements.edit',
            'admin.page-content' => 'pages.view',
            'admin.page-content.create' => 'pages.create',
            'admin.page-content.edit' => 'pages.edit',
            'admin.seo' => 'seo.view',
            'admin.seo.create' => 'seo.create',
            'admin.seo.edit' => 'seo.edit',
            'admin.site-setting' => 'site.view',
            'admin.general-setting' => 'general.view',
            'admin.shipping-setting' => 'shipping.view',
            'admin.profile-index' => 'profile.view',
            'admin.role-index' => 'roles.view',
            'admin.permission-index' => 'permissions.view',
            'admin.permission.create' => 'permissions.create',
        ];

        return $permissionMap[$routeName] ?? '';
    }
}
