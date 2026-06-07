<?php

namespace App\Models\frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrescription;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'session_id',
        'quantity',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    

    //  ADD THIS RELATIONSHIP
    public function prescription()
    {
        return $this->morphOne(ProductPrescription::class, 'prescriptionable');
    }
    
    // ADD THIS HELPER
    public function hasPrescription()
    {
        return $this->prescription()->exists();
    }
}