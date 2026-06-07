<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Annoucement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

        public function scopeActive($query)
{
    return $query->where('is_active', true)
        ->where(function($q) {
            $q->whereNull('start_at')
              ->orWhere('start_at', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('end_at')
              ->orWhere('end_at', '>=', now());
        });
}

protected static function booted()
{
    static::saved(function () {
        Cache::forget('announcements');
    });
    static::deleted(function () {
        Cache::forget('announcements');
    });
}

}
