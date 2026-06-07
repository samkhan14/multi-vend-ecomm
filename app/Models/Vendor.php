<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
     use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'business_type',
        'phone',
        'address',
        'city',
        'country',
        'status',
        'is_block',
        'vendor_type',
    ];


     /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function wallet()
    {
        return $this->hasOne(VendorWallet::class);
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function walletBalance()
    {
        return $this->walletTransactions()
            ->completed()
            ->sum('amount');
    }

public function bankDetail()
{
    return $this->hasOne(VendorBankDetail::class);
}

// Helper method to check if bank details exist
public function hasBankDetails()
{
    return $this->bankDetail !== null;
}

}
