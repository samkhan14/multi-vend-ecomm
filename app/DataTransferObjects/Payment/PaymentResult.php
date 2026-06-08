<?php

namespace App\DataTransferObjects\Payment;

readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $type,
        public ?string $redirectUrl = null,
        public ?string $message = null,
        public ?string $orderNumber = null,
    ) {}

    public static function redirect(string $url, string $orderNumber): self
    {
        return new self(
            success: true,
            type: 'redirect',
            redirectUrl: $url,
            orderNumber: $orderNumber,
        );
    }

    public static function completed(string $message = 'Payment initiated.', ?string $orderNumber = null): self
    {
        return new self(
            success: true,
            type: 'completed',
            message: $message,
            orderNumber: $orderNumber,
        );
    }

    public static function failed(string $message): self
    {
        return new self(
            success: false,
            type: 'failed',
            message: $message,
        );
    }
}
