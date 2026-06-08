<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\DataTransferObjects\Payment\PaymentResult;
use App\Enums\PaymentGateway;
use App\Models\Order;
use App\Models\OrderTranssections;
use App\Models\PaymentSetting;
use App\Services\Payment\NowPayments\NowPaymentsClient;
use App\Services\Payment\NowPaymentsPriceConverter;
use Illuminate\Support\Facades\Log;

class NowPaymentsGateway implements PaymentGatewayInterface
{
    public function __construct(
        private NowPaymentsClient $client,
        private NowPaymentsPriceConverter $priceConverter,
    ) {}

    public function slug(): string
    {
        return PaymentGateway::NowPayments->value;
    }

    public function supports(string $method): bool
    {
        return $method === $this->slug();
    }

    public function initiate(Order $order): PaymentResult
    {
        $settings = PaymentSetting::current();
        $generalSetting = getGeneralSetting();
        $baseCurrency = strtolower($generalSetting->currency ?? 'pkr');
        $gatewayCurrency = strtolower($settings->nowpayments_price_currency ?: 'usd');

        $baseAmount = (float) ($order->base_amount ?? $order->grand_total);
        $converted = $this->priceConverter->convertForGateway($baseAmount, $baseCurrency, $gatewayCurrency);

        $payload = [
            'price_amount' => $converted['amount'],
            'price_currency' => $converted['currency'],
            'order_id' => $order->order_number,
            'order_description' => 'Order '.$order->order_number,
            'ipn_callback_url' => route('webhooks.nowpayments'),
            'success_url' => route('checkout.thankyou', [
                'order_number' => $order->order_number,
                'source' => 'nowpayments',
            ]),
            'cancel_url' => route('checkout.payment.cancel', $order->order_number),
            'is_fixed_rate' => (bool) $settings->nowpayments_is_fixed_rate,
            'is_fee_paid_by_user' => (bool) $settings->nowpayments_fee_paid_by_user,
        ];

        if (filled($settings->nowpayments_pay_currency)) {
            $payload['pay_currency'] = strtolower($settings->nowpayments_pay_currency);
        }

        try {
            $invoice = $this->client->createInvoice($payload);
        } catch (\Throwable $exception) {
            Log::error('NOWPayments invoice creation failed', [
                'order_number' => $order->order_number,
                'message' => $exception->getMessage(),
            ]);

            return PaymentResult::failed('Unable to start crypto payment. Please try again or choose another payment method.');
        }

        OrderTranssections::query()->create([
            'order_id' => $order->id,
            'gateway' => $this->slug(),
            'transaction_id' => $invoice->paymentId,
            'gateway_payment_id' => $invoice->paymentId,
            'invoice_id' => $invoice->invoiceId,
            'amount' => $order->grand_total,
            'gateway_price_amount' => $converted['amount'],
            'gateway_price_currency' => $converted['currency'],
            'currency' => $order->order_currency ?? ($generalSetting->currency ?? 'PKR'),
            'payment_method' => $this->slug(),
            'payment_status' => 'waiting',
            'transaction_response' => $invoice->rawResponse,
        ]);

        if ($invoice->paymentId) {
            $order->update([
                'transaction_id' => $invoice->paymentId,
            ]);
        }

        return PaymentResult::redirect($invoice->invoiceUrl, $order->order_number);
    }
}
