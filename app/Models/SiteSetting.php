<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'favicon',
        'footer_logo',
        'website_logo',
        'admin_logo',
        'company_name',  
    ];

    protected static function booted()
{
    static::saved(function () {
        Cache::forget('site_setting');
    });
    static::deleted(function () {
        Cache::forget('site_setting');
    });
}
}