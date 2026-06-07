<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'level',
        'category_name',
        'category_image',
        'category_discount',
        'description',
        'url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'category_banner',
        'banner_status',
    ];

    // Category Model mein
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

        /**
     * Get products in this category
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Get active products in this category
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class, 'category_id')->where('status', 1);
    }
 /**
     * Get all descendants
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    protected static function booted()
{
    static::saved(function () {
        Cache::forget('navbar_categories');
    });
    static::deleted(function () {
        Cache::forget('navbar_categories');
    });
}
    
}
