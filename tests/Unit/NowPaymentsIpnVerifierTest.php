<?php

use App\Services\Payment\NowPayments\NowPaymentsIpnVerifier;

beforeEach(function () {
    config(['services.nowpayments.ipn_secret' => 'test-ipn-secret']);
});

it('accepts a valid NOWPayments IPN signature', function () {
    $payload = [
        'order_id' => 'ORD-ABC123',
        'payment_id' => 1001,
        'payment_status' => 'finished',
        'price_amount' => 49.99,
    ];

    $verifier = new NowPaymentsIpnVerifier;

    expect($verifier->verify(json_encode($payload), signNowPaymentsPayload($payload)))->toBeTrue();
});

it('rejects an invalid NOWPayments IPN signature', function () {
    $payload = [
        'order_id' => 'ORD-ABC123',
        'payment_id' => 1001,
        'payment_status' => 'finished',
    ];

    $verifier = new NowPaymentsIpnVerifier;

    expect($verifier->verify(json_encode($payload), 'invalid-signature'))->toBeFalse();
});

it('rejects tampered NOWPayments IPN payloads', function () {
    $payload = [
        'order_id' => 'ORD-ABC123',
        'payment_id' => 1001,
        'payment_status' => 'finished',
    ];

    $signature = signNowPaymentsPayload($payload);
    $payload['payment_status'] = 'waiting';

    $verifier = new NowPaymentsIpnVerifier;

    expect($verifier->verify(json_encode($payload), $signature))->toBeFalse();
});

it('rejects IPN verification when secret is missing', function () {
    config(['services.nowpayments.ipn_secret' => null]);

    $payload = ['order_id' => 'ORD-ABC123', 'payment_status' => 'finished'];
    $verifier = new NowPaymentsIpnVerifier;

    expect($verifier->verify(json_encode($payload), signNowPaymentsPayload($payload)))->toBeFalse();
});
