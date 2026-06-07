<?php

namespace App\Models\frontend;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Compare extends Model
{
    protected $table = 'compares';
    
    protected $fillable = [
        'product_id',
        'user_id',
        'session_id'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}