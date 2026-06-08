<?php

namespace App\Services\Payment\NowPayments;

use App\DataTransferObjects\Payment\NowPaymentsInvoiceData;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NowPaymentsClient
{
    public function status(): array
    {
        return $this->request('GET', 'status');
    }

    /**
     * Validates that the configured API key works for authenticated endpoints.
     *
     * @return array<string, mixed>
     */
    public function validateCredentials(): array
    {
        return $this->request('GET', 'currencies');
    }

    /**
     * @param  array<string, mixed>  $params
     */
    public function createInvoice(array $params): NowPaymentsInvoiceData
    {
        $response = $this->request('POST', 'invoice', $params);

        if (empty($response['invoice_url'])) {
            throw new RuntimeException('NOWPayments did not return an invoice URL.');
        }

        return new NowPaymentsInvoiceData(
            invoiceId: (string) ($response['id'] ?? $response['invoice_id'] ?? ''),
            paymentId: isset($response['payment_id']) ? (string) $response['payment_id'] : null,
            invoiceUrl: (string) $response['invoice_url'],
            rawResponse: $response,
        );
    }

    public function getPaymentStatus(int $paymentId): array
    {
        return $this->request('GET', 'payment/'.$paymentId);
    }

    /**
     * @return array<string, mixed>
     */
    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $apiKey = config('services.nowpayments.api_key');

        if (! filled($apiKey)) {
            throw new RuntimeException('NOWPayments API key is not configured.');
        }

        $url = rtrim((string) config('services.nowpayments.base_url'), '/').'/'.ltrim($endpoint, '/');

        $pendingRequest = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->acceptJson();

        try {
            $response = match (strtoupper($method)) {
                'GET' => $pendingRequest->get($url, $payload),
                'POST' => $pendingRequest->post($url, $payload),
                default => throw new RuntimeException("Unsupported HTTP method [{$method}]."),
            };
        } catch (RequestException $exception) {
            $body = $exception->response?->json() ?? [];
            $message = $body['message'] ?? $body['error'] ?? $exception->getMessage();

            throw new RuntimeException('NOWPayments API error: '.$message, previous: $exception);
        }

        if ($response->failed()) {
            $body = $response->json() ?? [];
            $message = $body['message'] ?? $body['error'] ?? $response->body();

            throw new RuntimeException('NOWPayments API error: '.$message);
        }

        return $response->json() ?? [];
    }
}
