<?php

namespace FattainNaime\PipraPay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use FattainNaime\PipraPay\Events\PipraPayPaymentCompleted;
use FattainNaime\PipraPay\Events\PipraPayWebhookReceived;
use FattainNaime\PipraPay\Facades\PipraPay;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        if (! PipraPay::validateWebhook($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        PipraPayWebhookReceived::dispatch($payload);

        if (isset($payload['status']) && $payload['status'] === 'completed') {
            PipraPayPaymentCompleted::dispatch($payload);
        }

        return response()->json(['message' => 'Webhook handled']);
    }
}
