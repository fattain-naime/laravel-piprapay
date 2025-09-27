<?php

namespace FattainNaime\PipraPay;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use FattainNaime\PipraPay\Exceptions\PipraPayApiException;

class PipraPayService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct(string $apiKey, bool $isSandbox, string $productionUrl, string $sandboxUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $isSandbox ? $sandboxUrl : $productionUrl;
    }

    /**
     * Generates a unique invoice ID in the format YYYYMMDDXXXXXX.
     * This 'pp_id' will also serve as the invoice ID.
     */
    protected function generatePpId(): string
    {
        $datePart = now()->format('Ymd');
        // Generate a 6-digit random number to ensure uniqueness
        $randomPart = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        return $datePart . $randomPart;
    }


    /**
     * Create a new payment charge.
     *
     * @param array $data The charge data.
     * @return array The API response as an array.
     * @throws PipraPayApiException
     */
    public function createCharge(array $data): array
    {
        // Add a unique ID that can be used as an invoice ID
        if (!isset($data['pp_id'])) {
            $data['pp_id'] = $this->generatePpId();
        }

        $response = $this->makeRequest('post', '/api/create-charge', $data);

        return $this->handleResponse($response);
    }

    /**
     * Verify a payment using its pp_id.
     *
     * @param string $pp_id The PipraPay payment ID.
     * @return array The API response as an array.
     * @throws PipraPayApiException
     */
    public function verifyPayment(string $pp_id): array
    {
        $response = $this->makeRequest('post', '/api/verify-payments', ['pp_id' => $pp_id]);

        return $this->handleResponse($response);
    }

    /**
     * Initiates a payment and returns the redirect URL.
     *
     * @param array $data
     * @return string
     * @throws PipraPayApiException
     */
    public function getPaymentUrl(array $data): string
    {
        $response = $this->createCharge($data);
        return $response['pp_url'];
    }

    /**
     * Validates an incoming webhook request.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function validateWebhook(\Illuminate\Http\Request $request): bool
    {
        $receivedApiKey = $request->header('mh-piprapay-api-key');
        return $receivedApiKey === $this->apiKey;
    }

    protected function makeRequest(string $method, string $uri, array $data = []): Response
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'mh-piprapay-api-key' => $this->apiKey,
        ])
        ->{$method}($this->baseUrl . $uri, $data);
    }

    protected function handleResponse(Response $response): array
    {
        if ($response->failed()) {
            throw new PipraPayApiException(
                "PipraPay API request failed: " . $response->reason(),
                $response->status()
            );
        }

        $body = $response->json();

        if (isset($body['status']) && $body['status'] === false) {
            throw new PipraPayApiException($body['message'] ?? 'Unknown PipraPay API error.');
        }

        return $body;
    }
}
