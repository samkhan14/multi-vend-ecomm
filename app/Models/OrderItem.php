<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductPrescription;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'variant_attributes',
        'price',
        'quantity',
        'discount',
        'tax',
        'commission',
        'final_price',
        'wallet_added',
        'subtotal',
        'status',
           'base_price',
    'base_subtotal',
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'commission' => 'decimal:2',
        'final_price' => 'decimal:2',
        'wallet_added' => 'boolean',
        'subtotal' => 'decimal:2',
        'variant_attributes' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

     //  ADD THIS RELATIONSHIP
    public function prescription()
    {
        return $this->morphOne(ProductPrescription::class, 'prescriptionable');
    }
    
    //  ADD THIS HELPER
    public function hasPrescription()
    {
        return $this->prescription()->exists();
    }
}
