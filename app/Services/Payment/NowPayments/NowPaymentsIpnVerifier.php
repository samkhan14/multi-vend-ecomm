<?php

namespace App\Services\Payment\NowPayments;

class NowPaymentsIpnVerifier
{
    public function verify(string $rawBody, ?string $signature): bool
    {
        $secret = config('services.nowpayments.ipn_secret');

        if (! filled($secret) || ! filled($signature)) {
            return false;
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return false;
        }

        ksort($payload);

        $sortedJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $expected = hash_hmac('sha512', $sortedJson, trim((string) $secret));

        return hash_equals($expected, $signature);
    }
}
