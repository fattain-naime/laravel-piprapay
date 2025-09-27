<?php

namespace Naime\PipraPay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Naime\PipraPay\Exceptions\InvalidApiKeyException;
use App\Models\Payment; // change this to your actual Payment model

class PipraPayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $headers = $request->headers->all();
        $receivedApiKey = $headers['mh-piprapay-api-key'][0] ?? 
                          $headers['Mh-Piprapay-Api-Key'][0] ?? 
                          $request->server('HTTP_MH_PIPRAPAY_API_KEY');

        if ($receivedApiKey !== config('piprapay.api_key')) {
            throw new InvalidApiKeyException('Unauthorized request. API Key mismatch.');
        }

        $data = $request->all();

        // Idempotency check
        if (Payment::where('pp_id', $data['pp_id'])->exists()) {
            return response()->json(['status' => true, 'message' => 'Already processed']);
        }

        // Save or process payment
        Payment::create([
            'pp_id' => $data['pp_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'],
            'metadata' => json_encode($data['metadata'] ?? []),
        ]);

        return response()->json(['status' => true, 'message' => 'Webhook received']);
    }
}
