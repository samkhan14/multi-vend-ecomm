<?php

use App\Models\GeneralSetting;
use App\Models\SiteSetting;
use App\Models\Vendor;

if (!function_exists('formatCurrency')) {
    /**
     * Format amount with currency symbol from general settings
     * 
     * @param float|int $amount
     * @param bool $showSymbol - Whether to show currency symbol
     * @return string
     */
    function formatCurrency($amount, $showSymbol = true)
    {
        $settings = GeneralSetting::first();

        // Default values if settings not found
        $symbol = $settings->currency_symbol ?? '$';
        $currency = $settings->currency ?? 'USD';

        // Format the amount with 2 decimal places
        $formattedAmount = number_format($amount, 2);

        if ($showSymbol) {
            return $symbol . $formattedAmount;
        }

        return $formattedAmount;
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Get currency symbol from general settings
     * 
     * @return string
     */
    function getCurrencySymbol()
    {
        $settings = GeneralSetting::first();
        return $settings->currency_symbol ?? '$';
    }
}

if (!function_exists('getCurrencyCode')) {
    /**
     * Get currency code from general settings
     * 
     * @return string
     */
    function getCurrencyCode()
    {
        $settings = GeneralSetting::first();
        return $settings->currency ?? 'USD';
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format price with currency symbol (alias for formatCurrency)
     * 
     * @param float|int $price
     * @return string
     */
    function formatPrice($price)
    {
        return formatCurrency($price);
    }
}

if (! function_exists('siteSetting')) {

    function siteSetting()
    {
        // 1 row hi hoti hai, is liye first()
        return SiteSetting::first();
    }
}

if (!function_exists('admin_vendor_id')) {
    function admin_vendor_id()
    {
        static $adminVendorId = null;

        if ($adminVendorId !== null) {
            return $adminVendorId;
        }

        $adminVendorId = Vendor::where('vendor_type', 'super_admin')->value('id');

        if (!$adminVendorId) {
            throw new Exception('Admin vendor not found');
        }

        return $adminVendorId;
    }
}

if (!function_exists('vendor_or_admin_id')) {
    function vendor_or_admin_id()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('Vendor')) {
            return $user->vendorId();
        }

        return admin_vendor_id();
    }
}
