<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Integration extends Model
{
    protected $fillable = [
        'google_console', 'google_analytics', 'google_tag_manager',
        'sitemap_submission', 'robots_txt', 'meta_tags', 'schema_markup', 'on_page_scripts',
        'live_chat', 'whatsapp_chat', 'messenger_chat', 'chatbot_scripts',
        'country_code', 'phone_number', 'whatsapp_on',
        'facebook_pixel', 'conversion_tracking', 'remarketing_tags'
    ];

    protected $casts = [
        'whatsapp_on' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($integration) {
            // Auto-generate robots.txt file
            if ($integration->wasChanged('robots_txt')) {
                $path = public_path('robots.txt');
                $content = $integration->robots_txt ?? "User-agent: *\nAllow: /";
                
                try {
                    File::put($path, $content);
                } catch (\Exception $e) {
                    \Log::error("Failed to sync robots.txt: " . $e->getMessage());
                }
            }

            // Auto-generate sitemap.xml file
            if ($integration->wasChanged('sitemap_submission')) {
                $path = public_path('sitemap.xml');
                $content = $integration->sitemap_submission ?? "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n</urlset>";
                
                try {
                    File::put($path, $content);
                } catch (\Exception $e) {
                    \Log::error("Failed to sync sitemap: " . $e->getMessage());
                }
            }
        });
    }
}