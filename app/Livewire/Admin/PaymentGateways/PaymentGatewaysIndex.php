<?php

namespace App\Livewire\Admin\PaymentGateways;

use App\Models\PaymentSetting;
use App\Services\Payment\NowPayments\NowPaymentsClient;
use Livewire\Component;
use RuntimeException;

class PaymentGatewaysIndex extends Component
{
    public bool $codEnabled = true;

    public bool $nowpaymentsEnabled = false;

    public string $nowpaymentsPriceCurrency = 'usd';

    public ?string $nowpaymentsPayCurrency = null;

    public bool $nowpaymentsIsFixedRate = false;

    public bool $nowpaymentsFeePaidByUser = false;

    public ?string $connectionMessage = null;

    public string $connectionStatus = 'idle';

    public function mount(): void
    {
        $settings = PaymentSetting::current();

        $this->codEnabled = (bool) $settings->cod_enabled;
        $this->nowpaymentsEnabled = (bool) $settings->nowpayments_enabled;
        $this->nowpaymentsPriceCurrency = strtolower($settings->nowpayments_price_currency ?: 'usd');
        $this->nowpaymentsPayCurrency = $settings->nowpayments_pay_currency;
        $this->nowpaymentsIsFixedRate = (bool) $settings->nowpayments_is_fixed_rate;
        $this->nowpaymentsFeePaidByUser = (bool) $settings->nowpayments_fee_paid_by_user;
    }

    public function save(): void
    {
        $this->authorize('payment_gateways.view');

        $validated = $this->validate([
            'codEnabled' => 'boolean',
            'nowpaymentsEnabled' => 'boolean',
            'nowpaymentsPriceCurrency' => 'required|string|max:10',
            'nowpaymentsPayCurrency' => 'nullable|string|max:20',
            'nowpaymentsIsFixedRate' => 'boolean',
            'nowpaymentsFeePaidByUser' => 'boolean',
        ]);

        if (! $validated['codEnabled'] && ! $validated['nowpaymentsEnabled']) {
            $this->addError('codEnabled', 'At least one payment gateway must remain enabled.');

            return;
        }

        if ($validated['nowpaymentsEnabled'] && ! PaymentSetting::current()->isNowPaymentsConfigured()) {
            $this->addError('nowpaymentsEnabled', 'NOWPayments API key is missing from environment configuration.');

            return;
        }

        $settings = PaymentSetting::query()->first() ?? new PaymentSetting;

        $settings->fill([
            'cod_enabled' => $validated['codEnabled'],
            'nowpayments_enabled' => $validated['nowpaymentsEnabled'],
            'nowpayments_price_currency' => strtolower($validated['nowpaymentsPriceCurrency']),
            'nowpayments_pay_currency' => filled($validated['nowpaymentsPayCurrency'])
                ? strtolower($validated['nowpaymentsPayCurrency'])
                : null,
            'nowpayments_is_fixed_rate' => $validated['nowpaymentsIsFixedRate'],
            'nowpayments_fee_paid_by_user' => $validated['nowpaymentsFeePaidByUser'],
        ])->save();

        $this->dispatch('show-toast', type: 'success', message: 'Payment gateway settings saved successfully.');
    }

    public function testConnection(NowPaymentsClient $client): void
    {
        $this->authorize('payment_gateways.view');

        $this->connectionStatus = 'testing';
        $this->connectionMessage = null;

        try {
            $client->validateCredentials();
            $response = $client->status();
            $message = $response['message'] ?? 'NOWPayments API connection successful.';
            $environment = config('services.nowpayments.sandbox') ? 'sandbox' : 'production';
            $this->connectionStatus = 'success';
            $this->connectionMessage = (string) $message.' ('.$environment.' API key verified)';
        } catch (RuntimeException $exception) {
            $this->connectionStatus = 'error';
            $this->connectionMessage = $this->formatConnectionError($exception->getMessage());
        }
    }

    private function formatConnectionError(string $message): string
    {
        if (str_contains(strtolower($message), 'invalid api key')) {
            $environment = config('services.nowpayments.sandbox') ? 'sandbox' : 'production';

            return 'Invalid API key for '.$environment.' mode. '
                .(config('services.nowpayments.sandbox')
                    ? 'Use a Sandbox API key from NOWPayments dashboard, or set NOWPAYMENTS_SANDBOX=false to use your production key.'
                    : 'Use a Production API key from NOWPayments dashboard, or set NOWPAYMENTS_SANDBOX=true with a sandbox key.');
        }

        return $message;
    }

    public function render()
    {
        $this->authorize('payment_gateways.view');

        $settings = PaymentSetting::current();

        return view('livewire.admin.payment-gateways.payment-gateways-index', [
            'apiKeyConfigured' => $settings->isNowPaymentsConfigured(),
            'ipnSecretConfigured' => filled(config('services.nowpayments.ipn_secret')),
            'sandboxEnabled' => (bool) config('services.nowpayments.sandbox'),
        ])->layout('layouts.admin');
    }
}
