<?php

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\Models\PaymentSetting;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /**
     * @param  iterable<PaymentGatewayInterface>  $gateways
     */
    public function __construct(private iterable $gateways) {}

    public function resolve(string $method): PaymentGatewayInterface
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->supports($method)) {
                return $gateway;
            }
        }

        throw new InvalidArgumentException("Unsupported payment method [{$method}].");
    }

    /**
     * @return Collection<int, PaymentGatewayInterface>
     */
    public function enabled(): Collection
    {
        $settings = PaymentSetting::current();
        $enabledSlugs = $settings->enabledGatewaySlugs();

        return collect($this->gateways)
            ->filter(fn (PaymentGatewayInterface $gateway) => in_array($gateway->slug(), $enabledSlugs, true))
            ->values();
    }

    public function isEnabled(string $method): bool
    {
        return PaymentSetting::current()->enabledGatewaySlugs() === []
            ? false
            : in_array($method, PaymentSetting::current()->enabledGatewaySlugs(), true);
    }
}
