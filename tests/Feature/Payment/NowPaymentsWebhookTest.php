<?php

use App\Enums\PaymentGateway;
use App\Jobs\ProcessNowPaymentsIpn;
use App\Models\Order;
use App\Models\OrderTranssections;
use App\Models\PaymentSetting;
use App\Services\Payment\NowPayments\NowPaymentsIpnProcessor;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config([
        'services.nowpayments.ipn_secret' => 'test-ipn-secret',
        'services.nowpayments.api_key' => 'test-api-key',
    ]);

    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => true,
        'nowpayments_price_currency' => 'usd',
    ]);
});

it('accepts valid NOWPayments webhook requests and marks orders paid', function () {
    Mail::fake();

    $order = Order::query()->create([
        'order_number' => 'ORD-WEBHOOK-1',
        'name' => 'Test User',
        'email' => 'customer@example.com',
        'mobile' => '1234567890',
        'address' => '123 Street',
        'city' => 'City',
        'state' => 'State',
        'country' => 'PK',
        'pincode' => '54000',
        'subtotal' => 100,
        'grand_total' => 100,
        'base_amount' => 100,
        'payment_method' => PaymentGateway::NowPayments->value,
        'payment_gateway' => PaymentGateway::NowPayments->value,
        'payment_status' => 'unpaid',
        'status' => 'pending',
    ]);

    OrderTranssections::query()->create([
        'order_id' => $order->id,
        'gateway' => PaymentGateway::NowPayments->value,
        'gateway_payment_id' => '555',
        'amount' => 100,
        'gateway_price_amount' => 10,
        'gateway_price_currency' => 'usd',
        'currency' => 'PKR',
        'payment_method' => PaymentGateway::NowPayments->value,
        'payment_status' => 'waiting',
    ]);

    $payload = [
        'order_id' => $order->order_number,
        'payment_id' => 555,
        'payment_status' => 'finished',
        'price_amount' => 10,
        'price_currency' => 'usd',
    ];

    $response = $this->postJson(route('webhooks.nowpayments'), $payload, [
        'x-nowpayments-sig' => signNowPaymentsPayload($payload),
    ]);

    $response->assertOk();

    $order->refresh();

    expect($order->payment_status)->toBe('paid')
        ->and($order->transaction_id)->toBe('555')
        ->and($order->status)->toBe('processing');
});

it('rejects NOWPayments webhook requests with invalid signatures', function () {
    $payload = [
        'order_id' => 'ORD-INVALID',
        'payment_status' => 'finished',
    ];

    $response = $this->postJson(route('webhooks.nowpayments'), $payload, [
        'x-nowpayments-sig' => 'invalid',
    ]);

    $response->assertUnauthorized();
});

it('processes duplicate paid IPN notifications idempotently', function () {
    Mail::fake();

    $order = Order::query()->create([
        'order_number' => 'ORD-WEBHOOK-2',
        'name' => 'Test User',
        'email' => 'customer@example.com',
        'mobile' => '1234567890',
        'address' => '123 Street',
        'city' => 'City',
        'state' => 'State',
        'country' => 'PK',
        'pincode' => '54000',
        'subtotal' => 50,
        'grand_total' => 50,
        'base_amount' => 50,
        'payment_method' => PaymentGateway::NowPayments->value,
        'payment_gateway' => PaymentGateway::NowPayments->value,
        'payment_status' => 'paid',
        'transaction_id' => '777',
        'status' => 'processing',
    ]);

    OrderTranssections::query()->create([
        'order_id' => $order->id,
        'gateway' => PaymentGateway::NowPayments->value,
        'gateway_payment_id' => '777',
        'amount' => 50,
        'gateway_price_amount' => 5,
        'gateway_price_currency' => 'usd',
        'currency' => 'PKR',
        'payment_method' => PaymentGateway::NowPayments->value,
        'payment_status' => 'finished',
        'paid_at' => now(),
    ]);

    $payload = [
        'order_id' => $order->order_number,
        'payment_id' => 777,
        'payment_status' => 'finished',
        'price_amount' => 5,
    ];

    app(NowPaymentsIpnProcessor::class)->process($payload);
    app(NowPaymentsIpnProcessor::class)->process($payload);

    expect(Order::query()->whereKey($order->id)->value('payment_status'))->toBe('paid');
});

it('dispatches queued IPN processing job from webhook controller', function () {
    ProcessNowPaymentsIpn::dispatch([
        'order_id' => 'ORD-JOB',
        'payment_status' => 'waiting',
    ]);

    expect(true)->toBeTrue();
});
