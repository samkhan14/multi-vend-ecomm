<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'status',
        'request_note',
        'admin_note',
        'processed_by',
        'processed_at',
        'wallet_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}

