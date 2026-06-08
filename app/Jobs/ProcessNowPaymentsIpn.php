<?php

namespace App\Jobs;

use App\Services\Payment\NowPayments\NowPaymentsIpnProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessNowPaymentsIpn implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload) {}

    public function handle(NowPaymentsIpnProcessor $processor): void
    {
        $processor->process($this->payload);
    }
}
