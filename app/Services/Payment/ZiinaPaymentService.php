<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\Facades\Http;

class ZiinaPaymentService
{
    /**
     * The Ziina API base URL
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * The Ziina API key/token
     * @var string
     */
    protected $apiToken;

    /**
     * Whether to use test mode
     * @var bool
     */
    protected $testMode;
    protected $ziinaWebhookSecret;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiBaseUrl = config('services.ziina.base_url', 'https://api-v2.ziina.com/api');
        $this->apiToken = config('services.ziina.api_token');
        $this->testMode = config('services.ziina.test_mode', false);
        $this->ziinaWebhookSecret = config('services.ziina.webhook_secret');

    }

    /**
     * Create a payment intent
     *
     * @param float $amount The payment amount
     * @param string $currencyCode The currency code (e.g., 'AED')
     * @param string $message Optional payment message
     * @param string $successUrl URL to redirect after successful payment
     * @param string $cancelUrl URL to redirect if payment is canceled
     * @param string $failureUrl URL to redirect if payment fails
     * @param string|null $expiry Payment expiry time (optional)
     * @return array|null The API response or null on failure
     */
    public function createPaymentIntent(
        float $amount,
        string $currency,
        string $message,
        string $successUrl,
        string $cancelUrl,
        string $failureUrl,
        string $expiry
    ) {
        try {
            $data = [
                'amount' => $amount,
                'currency_code' => $currency,
                'message' => $message,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'failure_url' => $failureUrl,
                'test' => $this->testMode,
                'transaction_source' => 'directApi',
                'expiry' => $expiry,
            ];
            return $this->makeRequest('payment_intent', 'POST', $data);
        } catch (Exception $e) {
            Log::error('Ziina payment intent creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Make an HTTP request to the Ziina API
     *
     * @param string $endpoint The API endpoint
     * @param string $method The HTTP method
     * @param array $data The request data
     * @return array|null The API response or null on failure
     */
    protected function makeRequest(string $endpoint, string $method = 'GET', array $data = [])
    {
        $curl = curl_init();

        $url = rtrim($this->apiBaseUrl, '/') . '/' . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json'
        ];
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === 'POST' && !empty($data)) {
            $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::error('Ziina API cURL Error: ' . $err);
            return null;
        }

        return json_decode($response, true);
    }

    public function registerWebhook(string $url, string $secret)
    {
        try {
            $data = [
                'url' => $url,
                'secret' => $this->ziinaWebhookSecret
            ];

            return $this->makeRequest('webhook', 'POST', $data);
        } catch (Exception $e) {
            Log::error('Ziina webhook registration failed: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $calculatedSignature = hash_hmac('sha256', $payload, $this->ziinaWebhookSecret);
        return hash_equals($calculatedSignature, $signature);
    }

    public function checkPaymentStatus(string $paymentIntentId)
    {
        try {
            return $this->makeRequest("payment_intent/{$paymentIntentId}", 'GET');
        } catch (Exception $e) {
            Log::error('Ziina payment status check failed: ' . $e->getMessage());
            return null;
        }
    }

    public function updateBitrixInvoiceStatus($invoiceId)
    {
        try {
            $webhook = "gifsocgxnsit098f";
            $userId = 1;
            $baseUrl = 'https://crm.cresco.ae/rest/' . $userId . '/' . $webhook . '/';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->withOptions([
                'verify' => false
            ])
            ->post($baseUrl . 'crm.invoice.update', [
                'ID' => $invoiceId,
                'FIELDS' => [
                    'STATUS_ID' => 3
                ]
            ]);

            // Optional: Handle the response
            if ($response->successful() && $response->json('result') === true) {
                return response()->json(['message' => 'Invoice status updated successfully.']);
            } else {
                return response()->json([
                    'error' => 'Failed to update invoice status',
                    'details' => $response->json()
                ], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Ziina Bitrix invoice status update failed: ' . $e->getMessage());
            return null;
        }
    }
}
