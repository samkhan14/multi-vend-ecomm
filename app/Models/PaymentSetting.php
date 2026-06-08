<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentSetting extends Model
{
    protected $fillable = [
        'cod_enabled',
        'nowpayments_enabled',
        'nowpayments_price_currency',
        'nowpayments_pay_currency',
        'nowpayments_is_fixed_rate',
        'nowpayments_fee_paid_by_user',
    ];

    protected function casts(): array
    {
        return [
            'cod_enabled' => 'boolean',
            'nowpayments_enabled' => 'boolean',
            'nowpayments_is_fixed_rate' => 'boolean',
            'nowpayments_fee_paid_by_user' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('payment_settings');
        });

        static::deleted(function (): void {
            Cache::forget('payment_settings');
        });
    }

    public static function current(): self
    {
        return Cache::remember('payment_settings', 3600, function () {
            return static::query()->first() ?? static::query()->create([
                'cod_enabled' => true,
                'nowpayments_enabled' => false,
                'nowpayments_price_currency' => 'usd',
            ]);
        });
    }

    public function isNowPaymentsConfigured(): bool
    {
        return filled(config('services.nowpayments.api_key'));
    }

    /**
     * @return array<int, string>
     */
    public function enabledGatewaySlugs(): array
    {
        $gateways = [];

        if ($this->cod_enabled) {
            $gateways[] = 'cod';
        }

        if ($this->nowpayments_enabled && $this->isNowPaymentsConfigured()) {
            $gateways[] = 'nowpayments';
        }

        return $gateways;
    }
}
