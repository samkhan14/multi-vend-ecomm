<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\DataTransferObjects\Payment\PaymentResult;
use App\Enums\PaymentGateway;
use App\Models\Order;

class CodGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return PaymentGateway::Cod->value;
    }

    public function supports(string $method): bool
    {
        return $method === $this->slug();
    }

    public function initiate(Order $order): PaymentResult
    {
        return PaymentResult::completed('Cash on delivery order placed.', $order->order_number);
    }
}
