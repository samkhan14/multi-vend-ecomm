<?php

namespace App\DataTransferObjects\Payment;

readonly class NowPaymentsInvoiceData
{
    public function __construct(
        public string $invoiceId,
        public ?string $paymentId,
        public string $invoiceUrl,
        public array $rawResponse,
    ) {}
}
