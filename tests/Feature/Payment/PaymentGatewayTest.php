<?php

use App\Models\PaymentSetting;
use App\Models\User;
use App\Services\Payment\Gateways\CodGateway;
use App\Services\Payment\Gateways\NowPaymentsGateway;
use App\Services\Payment\PaymentGatewayManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    $role->syncPermissions(Permission::all());

    $this->admin = User::factory()->create([
        'user_type' => 'adminpanel',
    ]);
    $this->admin->assignRole($role);
});

it('returns enabled gateways based on admin toggles', function () {
    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => false,
        'nowpayments_price_currency' => 'usd',
    ]);

    $manager = new PaymentGatewayManager([
        app(CodGateway::class),
        app(NowPaymentsGateway::class),
    ]);

    expect($manager->enabled())->toHaveCount(1)
        ->and($manager->enabled()->first()->slug())->toBe('cod');
});

it('includes nowpayments when enabled and api key exists', function () {
    config(['services.nowpayments.api_key' => 'test-api-key']);

    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => true,
        'nowpayments_price_currency' => 'usd',
    ]);

    $manager = new PaymentGatewayManager([
        app(CodGateway::class),
        app(NowPaymentsGateway::class),
    ]);

    expect($manager->enabled())->toHaveCount(2);
});

it('saves payment gateway settings from admin panel', function () {
    config(['services.nowpayments.api_key' => 'test-api-key']);

    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => false,
        'nowpayments_price_currency' => 'usd',
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\PaymentGateways\PaymentGatewaysIndex::class)
        ->set('nowpaymentsEnabled', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(PaymentSetting::query()->first()->nowpayments_enabled)->toBeTrue();
});

it('prevents disabling all payment gateways in admin panel', function () {
    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => false,
        'nowpayments_price_currency' => 'usd',
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\PaymentGateways\PaymentGatewaysIndex::class)
        ->set('codEnabled', false)
        ->set('nowpaymentsEnabled', false)
        ->call('save')
        ->assertHasErrors(['codEnabled']);
});

it('tests nowpayments api connection from admin panel', function () {
    config(['services.nowpayments.api_key' => 'test-api-key']);

    PaymentSetting::query()->create([
        'cod_enabled' => true,
        'nowpayments_enabled' => false,
        'nowpayments_price_currency' => 'usd',
    ]);

    config([
        'services.nowpayments.sandbox' => false,
        'services.nowpayments.base_url' => 'https://api.nowpayments.io/v1/',
        'services.nowpayments.api_key' => 'test-api-key',
    ]);

    \Illuminate\Support\Facades\Http::fake([
        '*' => \Illuminate\Support\Facades\Http::sequence()
            ->push(['currencies' => ['btc']], 200)
            ->push(['message' => 'OK'], 200),
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\PaymentGateways\PaymentGatewaysIndex::class)
        ->call('testConnection')
        ->assertSet('connectionStatus', 'success');
});
