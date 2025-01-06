<?php

namespace App\Repositories;

use App\Services\Bitrix\BitrixCredentialResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class BitrixAPIRepository
{
    protected $client;
    protected $baseUrl;
    protected $userId;
    protected $webhookToken;

    public function __construct(BitrixCredentialResolver $credentialResolver, $userId = null)
    {
        $credentials = $credentialResolver->resolveCredentials($userId);

        $this->baseUrl = $credentials['base_url'];
        $this->userId = $credentials['user_id'];
        $this->webhookToken = $credentials['webhook_token'];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }
    private function buildUrl($endPoint)
    {
        return "rest/{$this->userId}/{$this->webhookToken}/{$endPoint}";
    }
    public function call($endpoint, $params = [])
    {
        $url = $this->buildUrl($endpoint);
        try {
            $response = $this->client->request('POST', $url, [
                'query' => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("Bitrix API call failed: {$e->getMessage()}");
        }
    }

    public function getAllBitrixUsers()
    {
        $users = [];
        $start = 0;

        do {
            $response = $this->call('user.get', ['start' => $start]);

            if (isset($response['result']) && is_array($response['result'])) {
                $users = array_merge($users, $response['result']);
            }

            $start = $response['next'] ?? null;
        } while ($start !== null);

        return $users;
    }
}