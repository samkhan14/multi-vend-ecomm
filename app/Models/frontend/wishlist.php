<?php

namespace App\Models\frontend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    
    protected $fillable = ['product_id', 'user_id', 'session_id'];
    
    protected $table = 'wishlists'; 
    
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}