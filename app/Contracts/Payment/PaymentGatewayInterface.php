<?php

namespace App\Contracts\Payment;

use App\DataTransferObjects\Payment\PaymentResult;
use App\Models\Order;

interface PaymentGatewayInterface
{
    public function slug(): string;

    public function supports(string $method): bool;

    public function initiate(Order $order): PaymentResult;
}
