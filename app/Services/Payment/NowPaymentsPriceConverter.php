<?php

namespace App\Services\Payment;

class NowPaymentsPriceConverter
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED_FIAT = [
        'usd', 'eur', 'ngn', 'cad', 'aud', 'gbp', 'krw', 'ils', 'ron', 'ars',
        'inr', 'idr', 'mxn', 'myr', 'try', 'clp', 'pen', 'php', 'thb', 'vnd', 'pln', 'brl',
    ];

    /**
     * @return array{amount: float, currency: string}
     */
    public function convertForGateway(float $baseAmount, string $baseCurrency, string $targetCurrency): array
    {
        $baseCurrency = strtolower($baseCurrency);
        $targetCurrency = strtolower($targetCurrency);

        if ($baseCurrency === $targetCurrency) {
            return [
                'amount' => round($baseAmount, 2),
                'currency' => $targetCurrency,
            ];
        }

        if (in_array($baseCurrency, self::SUPPORTED_FIAT, true)) {
            $rate = getCurrencyRate(strtoupper($baseCurrency), strtoupper($targetCurrency));

            return [
                'amount' => round($baseAmount * $rate, 2),
                'currency' => $targetCurrency,
            ];
        }

        $rateToTarget = getCurrencyRate(strtoupper($baseCurrency), strtoupper($targetCurrency));

        return [
            'amount' => round($baseAmount * $rateToTarget, 2),
            'currency' => $targetCurrency,
        ];
    }
}
