<?php

namespace App\Repositories;

use App\Services\Bitrix\BitrixCredentialResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixApiRepository
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
            'verify' => false,
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
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
    public function getHostedCompanies()
    {
        try {
            Log::info("BitrixApiRepository: getHostedCompanies() | making HTTP request...");

            $response = $this->call('crm.company.compliance', [
                'action' => 'getHostedCompanies'
            ]);

            Log::info("BitrixApiRepository: getHostedCompanies() | API response received.");

            if (!is_array($response)) {
                Log::error("BitrixApiRepository: getHostedCompanies() | API response is invalid or not an array.", [
                    'response' => $response
                ]);
                return [];
            }

            if (isset($response['error'])) {
                Log::error("BitrixApiRepository: API request failed.", [
                    'error' => $response['error'],
                ]);
                return [];
            }

            Log::info("BitrixApiRepository: getHostedCompanies() | API request successful, returning companies.");

            return $response['result'] ?? [];

        } catch (\Exception $e) {
            Log::error("BitrixApiRepository: getHostedCompanies() | Error fetching hosted companies: " . $e->getMessage());
            return [];
        }
    }
    public function getCompanyData($companyId)
    {
        try {
            Log::info('BitrixApiRepository: getCompanyData() | Fetching company data from bitrix for company Id: ' . $companyId);
            $response = $this->call('crm.company.get', [
                'id' => $companyId
            ]);

            if (!is_array($response)) {
                Log::error("BitrixApiRepository: getHostedCompanies() | API response is invalid or not an array.", [
                    'response' => $response
                ]);
                return [];
            }

            if (isset($response['error'])) {
                Log::error("BitrixApiRepository: API request failed.", [
                    'error' => $response['error'],
                ]);
                return [];
            }

            Log::info('BitrixApiRepository: getCompanyData() | Data fetched successfully.');
            return $response['result'] ?? null;

        } catch (\Exception $e) {
            Log::error("BitrixApiRepository: getCompanyData() | Error fetching company data for ID {$companyId}: " . $e->getMessage());
            return null;
        }
    }
    public function getCompanyRelationships($companyId)
    {
        try {
            Log::info('BitrixApiRepository: getCompanyRelationships() | Fetching company relationships for ID: ' . $companyId);

            $response = $this->call('crm.company.compliance', [
                'action' => 'getCompanyRelationships',
                'company_id' => $companyId
            ]);

            return $response ? collect($response['result']) : collect();
        } catch (\Exception $e) {
            Log::error("Error fetching company relationships for ID {$companyId}: " . $e->getMessage());
            return collect();
        }
    }
    public function getOtherRelationships($companyId)
    {
        try {
            $response = $this->call('crm.company.compliance', [
                'action' => 'getOtherCompanyRelationships',
                'company_id' => $companyId
            ]);

            return collect($response['result'])  ?? collect();
        } catch (\Exception $e) {
            Log::error("Error fetching company other relationships for ID {$companyId}: " . $e->getMessage());
            return collect();
        }
    }
    public function getOrganizationType($typeId)
    {
        $response = $this->call('crm.company.userfield.get', [
            'id' => 258
        ]);

        if ($response){
            $organizationTypes = $response['result']['LIST'];
            foreach ($organizationTypes as $organizationType) {
                if ($organizationType['ID'] == $typeId) {
                    return $organizationType['VALUE'];
                }
            }
        }

        return null;
    }
    public function getContactList($relationshipContacts)
    {
        $contactIds = collect($relationshipContacts)->pluck('owner_id_reverse')->unique()->toArray();

        if (count($contactIds) > 0) {
            try{
                $response = $this->call('crm.contact.list', [
                    'select' => [
                        'ID', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'BIRTHDATE',
                        'UF_CRM_1539675920', 'UF_CRM_1539675930', 'UF_CRM_1621338479',
                        'UF_CRM_1539671799', 'UF_CRM_1539671852', 'UF_CRM_1539671987',
                        'UF_CRM_1666854161', 'UF_CRM_1539671927', 'UF_CRM_1656067869643',
                        'UF_CRM_1707806794', 'UF_CRM_1675085070282', 'UF_CRM_1656067944685',
                        'UF_CRM_1656068064883', 'UF_CRM_1547529289902', 'UF_CRM_1712312361019'
                    ],
                    'filter' => [
                        '=ID' => $contactIds
                    ]
                ]);

                return $response['result'] ?? [];
            } catch (\Exception $e) {
                Log::error("Error fetching contact list: " . $e->getMessage());
            }
        }

        return [];
    }
    public function getCompanyList($relationshipCompanies)
    {
        $companyIds = collect($relationshipCompanies)->pluck('owner_id_reverse')->unique()->toArray();

        if (count($companyIds) > 0) {
            try {
                $response = $this->call('crm.company.list', [
                    'select' => [
                        'TITLE', 'UF_CRM_1567591803', 'UF_CRM_1540706070',
                        'UF_CRM_1567589971', 'UF_CRM_1540705883',
                        'UF_CRM_1540705963'
                    ],
                    'filter' => [
                        '=ID' => $companyIds
                    ]
                ]);

                return $response['result'] ?? [];
            } catch (\Exception $e) {
                Log::error("Error fetching company list: " . $e->getMessage());
            }
        }

        return [];
    }
    public function getContactCountries()
    {
        try {
            $response = $this->call('crm.contact.userfield.get', [
                'id' => 859
            ]);

            return $response['result']['LIST'] ?? [];
        } catch (\Exception $e) {
            Log::error("Error fetching contact countries: " . $e->getMessage());
            return [];
        }
    }
    public function getPlaceOfBirthCountries()
    {
        try {
            $response = $this->call('crm.contact.userfield.get', [
                'id' => 1050
            ]);

            return $response['result']['LIST'] ?? [];
        } catch (\Exception $e) {
            Log::error("Error fetching place of birth countries: " . $e->getMessage());
            return [];
        }
    }
    public function getCompanyIncorporationCountries()
    {
        try {
            $response = $this->call('crm.company.userfield.get', [
                'id' => 478
            ]);

            return $response['result']['LIST'] ?? [];
        } catch (\Exception $e) {
            Log::error("Error fetching company incorporation countries: " . $e->getMessage());
            return [];
        }
    }
    public function getContactNominator($nominatorId)
    {
        try {
            $response = $this->call('crm.contact.list', [
                'select' => [
                    'ID', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'BIRTHDATE',
                    'UF_CRM_1539675920', 'UF_CRM_1539675930', 'UF_CRM_1621338479',
                    'UF_CRM_1539671799', 'UF_CRM_1539671852', 'UF_CRM_1539671987',
                    'UF_CRM_1666854161', 'UF_CRM_1539671927'
                ],
                'filter' => [
                    '=ID' => $nominatorId
                ]
            ]);
            return ($response && isset($response['result']['LIST'])) ? $response['result']['LIST'] : [];

        } catch (\Exception $e) {
            Log::error("Error fetching contact nominator: " . $e->getMessage());
            return [];
        }
    }
    public function getCompanyNominator($nominatorId)
    {
        try {
            $response = $this->call('crm.contact.list', [
                'select' => [
                    'TITLE', 'UF_CRM_1567591803', 'UF_CRM_1540706070', 'UF_CRM_1567589971',
                    'UF_CRM_1540705883', 'UF_CRM_1540705963'
                ],
                'filter' => [
                    '=ID' => $nominatorId
                ]
            ]);

            if ($response){
                return $response['result']['LIST'] ?? [];
            }

        } catch (\Exception $e) {
            Log::error("Error fetching company nominator: " . $e->getMessage());
            return [];
        }
    }
    public function getRelationshipRoles($companyId, $contactIds)
    {
        try {
            $response = $this->call('crm.company.compliance', [
                'action' => 'getContactRoleInCompany',
                'company_id' => $companyId,
                'contact_ids' => $contactIds->toArray()
            ]);

            if($response){
                return collect($response['result']) ?? collect();
            }

        } catch (\Exception $e) {
            Log::error("Error fetching relationships roles" . $e->getMessage());
            return collect();
        }
    }
}
