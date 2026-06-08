<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTranssections extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'gateway_payment_id',
        'invoice_id',
        'amount',
        'gateway_price_amount',
        'gateway_price_currency',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_price_amount' => 'decimal:2',
            'transaction_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
