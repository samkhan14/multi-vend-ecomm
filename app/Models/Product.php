<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'product_type',
        'vendor_id',
        'product_name',
        'product_slug',
        'product_price',
            'sale_price',         
    'sale_start_date',      
    'sale_end_date',
        'product_discount',
        'product_weight',
        'thumbnail_image',
        'short_description',
        'long_description',
        'stock',
        'stock_status',
        'is_featured',
        'order_by',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
        'view_count',           
    'interaction_count',    
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Product images
    public function images()
    {
        return $this->hasMany(ProductImages::class);
    }

    // Product.php
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }


    // app/Models/Product.php

/**
 * Check if product is from Glasses category
 */
public function isGlassesCategory()
{
    $glassesCategory = Category::whereRaw('LOWER(category_name) = ?', ['glasses'])->first();
    
    if (!$glassesCategory) {
        return false;
    }
    
    $category = $this->category;
    
    while ($category) {
        if ($category->id == $glassesCategory->id) {
            return true;
        }
        $category = $category->parent;
    }
    
    return false;
}

/**
 * Check if product is Normal Glasses
 */
public function isNormalGlasses()
{
    return $this->isGlassesCategory() && ($this->product_type ?? 'normal') === 'normal';
}
}
