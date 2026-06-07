<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_name',
        'page_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_author',
        'meta_robots',
        'meta_image',
        'og_title',
        'og_description',
        'og_type',
        'og_url',
    ];

public static function getMetadataForCurrentPage()
{
    $path = strtolower(request()->path());
    
    if ($path === '/' || $path === '' || $path === 'index' || $path === 'home') {
        return self::whereIn('page_url', ['home', 'landing', 'main', '/'])->first();
    }
    
    $mappings = [
        'about'   => ['aboutus', 'about-us', 'about_us', 'our-story'],
        'contact' => ['contactus', 'contact-us', 'contact_us', 'get-in-touch'],
        'shop'    => ['store', 'products', 'all-products'],
    ];

    $searchTerms = [$path];

    foreach ($mappings as $cleanKey => $aliases) {
        if (in_array($path, $aliases) || $path === $cleanKey) {
            $searchTerms = array_merge([$cleanKey], $aliases);
            break;
        }
    }

    $match = self::whereIn('page_url', $searchTerms)->first();
    if ($match) return $match;

    $normalizedPath = str_replace(['-', '_', ' '], '', $path);
    return self::all()->filter(function($page) use ($normalizedPath) {
        $dbSlug = str_replace(['-', '_', ' '], '', strtolower($page->page_url));
        return $dbSlug === $normalizedPath;
    })->first();
}
    

    protected static function booted()
    {
        static::saved(function () {
            // Clear all SEO caches (hard to know which URLs, so flush all)
            Cache::flush();
        });

        static::deleted(function () {
            Cache::flush();
        });
    }
}