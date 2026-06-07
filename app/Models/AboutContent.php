<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AboutContent extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
    ];


    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return asset('assets/images/default-about.jpg');
    }

     protected static function booted()
    {
        static::saved(function () {
            // Clear all about excerpt caches (different word counts)
            Cache::forget('about_excerpt_5');
            Cache::forget('about_excerpt_10');
            Cache::forget('about_excerpt_15');
            Cache::forget('about_excerpt_20');
        });

        static::deleted(function () {
            Cache::forget('about_excerpt_5');
            Cache::forget('about_excerpt_10');
            Cache::forget('about_excerpt_15');
            Cache::forget('about_excerpt_20');
        });
    }
}


