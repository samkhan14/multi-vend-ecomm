<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessNowPaymentsIpn;
use App\Services\Payment\NowPayments\NowPaymentsIpnVerifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NowPaymentsWebhookController extends Controller
{
    public function __invoke(Request $request, NowPaymentsIpnVerifier $verifier): Response
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-nowpayments-sig');

        if (! $verifier->verify($rawBody, $signature)) {
            Log::warning('NOWPayments IPN signature verification failed');

            return response('Invalid signature', Response::HTTP_UNAUTHORIZED);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        ProcessNowPaymentsIpn::dispatch($payload);

        return response('OK', Response::HTTP_OK);
    }
}
