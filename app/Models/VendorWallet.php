<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'available_balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'vendor_id', 'vendor_id');
    }

    /**
     * Methods
     */
    /**
     * Add available balance to the wallet.
     *
     * @param float $amount The amount to add.
     */

    public function addAvailableBalance(float $amount): void
    {
        $this->available_balance += $amount;
        $this->total_earned += $amount;
        $this->save();
    }

    public function deductAvailableBalance(float $amount): void
    {
        $this->available_balance -= $amount;
        $this->total_withdrawn += $amount;
        $this->save();
    }

    public function addPendingBalance(float $amount): void
    {
        $this->pending_balance += $amount;
        $this->save();
    }

    public function moveFromPendingToAvailable(float $amount): void
    {
        $this->pending_balance -= $amount;
        $this->available_balance += $amount;
        $this->save();
    }

    public function getTotalBalanceAttribute(): float
    {
        return $this->available_balance + $this->pending_balance;
    }

    public function getFormattedAvailableBalanceAttribute(): string
    {
        return number_format($this->available_balance, 2);
    }

    public function getFormattedPendingBalanceAttribute(): string
    {
        return number_format($this->pending_balance, 2);
    }

    public function getFormattedTotalEarnedAttribute(): string
    {
        return number_format($this->total_earned, 2);
    }

    public function getFormattedTotalWithdrawnAttribute(): string
    {
        return number_format($this->total_withdrawn, 2);
    }

    /**
     * Scopes
     */
    
    public function scopeWithAvailableBalance($query, float $minAmount = 0)
    {
        return $query->where('available_balance', '>=', $minAmount);
    }

    public function scopeWithPendingBalance($query, float $minAmount = 0)
    {
        return $query->where('pending_balance', '>=', $minAmount);
    }
}
