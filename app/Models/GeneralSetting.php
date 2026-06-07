<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'currency_symbol',
        'country_code',
        'phone',
        'email',
        'address',
        'commission',
    ];

    protected static function booted()
{
    static::saved(function () {
        Cache::forget('general_setting');
    });
    static::deleted(function () {
        Cache::forget('general_setting');
    });
}
}
