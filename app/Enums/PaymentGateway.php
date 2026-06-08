<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case Cod = 'cod';
    case NowPayments = 'nowpayments';
}
