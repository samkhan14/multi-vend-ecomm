<?php

namespace App\Services\Payment\NowPayments;

use App\Enums\NowPaymentsStatus;
use App\Enums\PaymentGateway;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderTranssections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NowPaymentsIpnProcessor
{
    public function process(array $payload): void
    {
        $orderNumber = (string) ($payload['order_id'] ?? '');
        $paymentId = isset($payload['payment_id']) ? (string) $payload['payment_id'] : null;
        $paymentStatus = NowPaymentsStatus::tryFrom(strtolower((string) ($payload['payment_status'] ?? '')));

        if ($orderNumber === '' || ! $paymentStatus) {
            Log::warning('NOWPayments IPN missing order_id or payment_status', [
                'payload' => $payload,
            ]);

            return;
        }

        $order = Order::query()->where('order_number', $orderNumber)->first();

        if (! $order) {
            Log::warning('NOWPayments IPN order not found', ['order_number' => $orderNumber]);

            return;
        }

        if ($order->payment_status === 'paid' && $paymentStatus->isPaid()) {
            return;
        }

        $transaction = OrderTranssections::query()
            ->where('order_id', $order->id)
            ->where('gateway', PaymentGateway::NowPayments->value)
            ->when($paymentId, fn ($query) => $query->where('gateway_payment_id', $paymentId))
            ->latest('id')
            ->first();

        if ($transaction && ! $this->amountMatches($transaction, $payload)) {
            Log::warning('NOWPayments IPN amount mismatch', [
                'order_number' => $orderNumber,
                'expected' => $transaction->gateway_price_amount,
                'received' => $payload['price_amount'] ?? null,
            ]);

            return;
        }

        DB::transaction(function () use ($order, $transaction, $payload, $paymentId, $paymentStatus): void {
            if ($transaction) {
                $transaction->update([
                    'gateway_payment_id' => $paymentId ?? $transaction->gateway_payment_id,
                    'transaction_id' => $paymentId ?? $transaction->transaction_id,
                    'payment_status' => $paymentStatus->value,
                    'transaction_response' => $payload,
                    'paid_at' => $paymentStatus->isPaid() ? now() : $transaction->paid_at,
                ]);
            }

            if ($paymentStatus->isPaid()) {
                $wasUnpaid = $order->payment_status !== 'paid';

                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $paymentId ?? $order->transaction_id,
                    'status' => $order->status === 'pending' ? 'processing' : $order->status,
                ]);

                if ($wasUnpaid) {
                    try {
                        Mail::to($order->email)->send(new OrderConfirmationMail($order->fresh('items')));
                    } catch (\Exception $exception) {
                        Log::error('NOWPayments confirmation email failed: '.$exception->getMessage());
                    }
                }

                return;
            }

            if ($paymentStatus->isFailed()) {
                $order->update([
                    'payment_status' => 'failed',
                    'transaction_id' => $paymentId ?? $order->transaction_id,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function amountMatches(OrderTranssections $transaction, array $payload): bool
    {
        if (! isset($payload['price_amount'])) {
            return true;
        }

        $received = (float) $payload['price_amount'];
        $expected = (float) $transaction->gateway_price_amount;

        return abs($received - $expected) <= 0.05;
    }
}
