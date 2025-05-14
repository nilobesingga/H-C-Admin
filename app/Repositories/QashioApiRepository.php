<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class QashioApiRepository
{
    protected $client;
    protected $baseUrl;
    protected $companyId;
    protected $accessToken;

    public function __construct()
    {
        $this->baseUrl = 'https://erp.qashio.com';
        $this->companyId = 'd179b6e8-a62d-411f-83cc-bba7c11a2be8';
        $this->accessToken = 'W5L3JdxS2GKQqV3fXA9prEUT323G6iYycfRn2cWD2mo=';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'companyId' => $this->companyId,
                'x-api-key' => $this->accessToken,
                'Accept' => 'application/json',
            ],
        ]);
    }
    private function buildUrl($endPoint)
    {
        return "{$this->baseUrl}/{$endPoint}";
    }
    public function call($endpoint, $params = [])
    {
        $url = $this->buildUrl($endpoint);
        try {
            $response = $this->client->request('GET', $url, [
                'query' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            Log::error('Qashio API GET request failed', [
                'endpoint' => $endpoint,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            // Return error details
            return [
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function getTransactions($params = [])
    {
        return $this->call('erp-transactions', $params);
    }
}
