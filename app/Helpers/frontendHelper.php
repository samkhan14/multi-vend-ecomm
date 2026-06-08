<?php

use App\Models\AboutContent;
use App\Models\Annoucement;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\PaymentSetting;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// ========== SITE SETTING ==========
function getSiteSetting()
{
    return Cache::remember('site_setting', 3600, function () {
        return SiteSetting::first();
    });
}

// ========== GENERAL SETTING ==========
function getGeneralSetting()
{
    return Cache::remember('general_setting', 3600, function () {
        return GeneralSetting::first();
    });
}

function getPaymentSettings(): PaymentSetting
{
    return PaymentSetting::current();
}

// ========== ANNOUNCEMENTS ==========
function getAnnouncements()
{
    return Cache::remember('announcements', 3600, function () {
        return Annoucement::active()->latest()->get();
    });
}

// ========== NAVBAR CATEGORIES ==========
function getNavbarCategories()
{
    return Cache::remember('navbar_categories', 3600, function () {
        return Category::where('level', 0)
            ->where('status', 1)
            ->orderBy('category_name')
            ->with(['children' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('category_name');
            }])
            ->get();
    });
}

// ========== FOOTER NAME (WITH CACHE) ==========
function getFooterName()
{
    $siteSetting = getSiteSetting();  // ← CACHE WALA USE KARO

    if ($siteSetting && ! empty($siteSetting->company_name)) {
        return $siteSetting->company_name;
    }

    return config('app.name', 'MM-MP');
}

// ========== ABOUT EXCERPT (WITH CACHE) ==========
function getAboutExcerpt($words = 5)
{
    $cacheKey = 'about_excerpt_'.$words;

    return Cache::remember($cacheKey, 3600, function () use ($words) {
        $about = AboutContent::first();

        if (! $about || empty($about->content)) {
            return 'Marketpro become the largest computer parts, gaming pc parts, and other IT related products.';
        }

        $plainText = strip_tags($about->content);
        $words_array = explode(' ', $plainText);
        $excerpt = implode(' ', array_slice($words_array, 0, $words));

        if (count($words_array) > $words) {
            $excerpt .= '...';
        }

        return $excerpt;
    });
}

function getSeoMetadata()
{

    return SeoSetting::getMetadataForCurrentPage();

}

// ========== CURRENCY CONVERSION ==========
function getUserCurrency()
{
    $generalSetting = getGeneralSetting();
    $baseCurrency = $generalSetting->currency ?? 'PKR';

    return session('user_currency', $baseCurrency);
}

function setUserCurrency($currency)
{
    session(['user_currency' => $currency]);
}

function getCurrencyRate($baseCurrency, $targetCurrency)
{
    if ($baseCurrency === $targetCurrency) {
        return 1;
    }

    $cacheKey = "exchange_rate_{$baseCurrency}_{$targetCurrency}";

    return Cache::remember($cacheKey, 43200, function () use ($baseCurrency, $targetCurrency) { // 12 hours cache
        try {
            $base = strtolower($baseCurrency);
            $target = strtolower($targetCurrency);

            // Free CDN API - No key required, unlimited requests
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$base}.json");

            if ($response->successful()) {
                $data = $response->json();

                // Check if rate exists in response
                if (isset($data[$base][$target])) {
                    return (float) $data[$base][$target];
                }
            }

            // Log error if API fails
            \Log::warning("Currency rate not found for {$baseCurrency} to {$targetCurrency}");

        } catch (\Exception $e) {
            \Log::error('Currency API Error: '.$e->getMessage());
        }

        // Fallback to 1 if everything fails
        return 1;
    });
}

function convertPrice($price)
{
    if (empty($price) || $price <= 0) {
        return $price;
    }

    $generalSetting = getGeneralSetting();
    if (! $generalSetting) {
        return $price;
    }

    $baseCurrency = $generalSetting->currency ?? 'PKR';
    $userCurrency = getUserCurrency();

    if ($baseCurrency === $userCurrency) {
        return $price;
    }

    $rate = getCurrencyRate($baseCurrency, $userCurrency);

    return $price * $rate;
}
