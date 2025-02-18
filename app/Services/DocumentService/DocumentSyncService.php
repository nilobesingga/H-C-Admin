<?php

namespace App\Services\DocumentService;

use App\Repositories\BitrixApiRepository;
use Illuminate\Support\Facades\Log;

class DocumentSyncService
{
    public $bitrixRepository;
    public $contactCountries;
    public $placeOfBirthCountries;
    public $companyIncorporationCountryList;
    public $individualDirectors = [];
    public $individualSecretaries = [];
    public $shareholders = [];
    public $ubos = [];
    public $nominees = [];
    public $founders = [];
    public $beneficiaries = [];
    public $protectors = [];
    public $councilors = [];
    public $authorizedPersons = [];
    public $officers = [];
    public $contacts = [];

    public function __construct(BitrixApiRepository $bitrixRepository)
    {
        Log::info("DocumentSyncService: BitrixApiRepository injected. Instance: " . get_class($bitrixRepository));

        $this->bitrixRepository = $bitrixRepository;
        $this->contactCountries =  $this->bitrixRepository->getContactCountries();
        $this->placeOfBirthCountries = $this->bitrixRepository->getPlaceOfBirthCountries();
        $this->companyIncorporationCountryList = $this->bitrixRepository->getCompanyIncorporationCountries();
    }
    public function getHostedCompanies()
    {
        Log::info("DocumentSyncService: fetched getHostedCompanies ......");

        // Log to confirm if repository instance is correct
        if (!$this->bitrixRepository) {
            Log::error("DocumentSyncService: BitrixApiRepository is not instantiated properly.");
            return [];
        }

        $companies = $this->bitrixRepository->getHostedCompanies();

        Log::info("DocumentSyncService: Companies fetched from BitrixApiRepository. Count: " . count($companies));

        // Apply filtering logic here
        $FSACompanies = collect($companies)->filter(function ($company) {
            return $company['country_of_incorporation'] === '1033' || $company['continuation_country'] === '1632';
        });

        Log::info('Total FSA companies: ' . $FSACompanies->count());

        return $FSACompanies;
    }
    public function getCompanyData($companyId)
    {
        return $this->bitrixRepository->getCompanyData($companyId);
    }
    public function getCompanyRelationships($companyId)
    {
         $this->individualDirectors = [];
         $this->individualSecretaries = [];
         $this->shareholders = [];
         $this->ubos = [];
         $this->nominees = [];
         $this->founders = [];
         $this->beneficiaries = [];
         $this->protectors = [];
         $this->councilors = [];
         $this->authorizedPersons = [];
         $this->officers = [];
         $this->contacts = [];

        $companyRelationships = $this->bitrixRepository->getCompanyRelationships($companyId);
        $otherRelationships = $this->bitrixRepository->getOtherRelationships($companyId);
        $mergedRelationships = $companyRelationships->merge($otherRelationships);


        $relationshipContacts = $mergedRelationships->filter(function ($item) {
            return $item['owner_type_reverse'] == '3';
        });

        $relationshipCompanies = $mergedRelationships->filter(function ($item) {
            return $item['owner_type_reverse'] == '4';
        });

        $this->contacts = $this->bitrixRepository->getContactList($relationshipContacts);
        $companiesContacts = $this->bitrixRepository->getCompanyList($relationshipCompanies);



        $this->prepareContact($relationshipContacts, $this->contacts);
        $this->prepareCompanyContact($relationshipCompanies, $companiesContacts);

        // Process each UBO and add nomineeList
        foreach ($this->ubos as &$ubo) {
            $ubo['nomineeList'] = [];

            // Find the matching nominee based on ID and typeId
            $nominee = collect($this->nominees)->first(function ($o) use ($ubo) {
                return $o['id'] == $ubo['nomineeId'] && $o['typeId'] == $ubo['nomineeType'];
            });

            // If a nominee is found, push it into the nomineeList
            if ($nominee) {
                $ubo['nomineeList'][] = $nominee;
            }
        }

        // Sorting function based on 'startDate'
        $this->sortByDate($this->individualDirectors, 'startDate');
        $this->sortByDate($this->shareholders, 'startDate');
        $this->sortByDate($this->ubos, 'startDate');
        $this->sortByDate($this->individualSecretaries, 'startDate');
        $this->sortByDate($this->individualSecretaries, 'startDate');

        return [
            'individualDirectors' => $this->individualDirectors,
            'individualSecretaries' => $this->individualSecretaries,
            'shareholders' => $this->shareholders,
            'ubos' => $this->ubos,
            'nominees' => $this->nominees,
            'founders' => $this->founders,
            'beneficiaries' => $this->beneficiaries,
            'protectors' => $this->protectors,
            'councilors' => $this->councilors,
            'authorizedPersons' => $this->authorizedPersons,
            'officers' => $this->officers,
            'contacts' => $this->contacts
        ];

    }
    public function getOrganizationType($typeId)
    {
        return $this->bitrixRepository->getOrganizationType($typeId);
    }
    public function getHensleyAndCookStatusText($statusId)
    {
        $statusesArray = [
            "22" => "Active",
            "23" => "Liquidated",
            "24" => "Dormant",
            "25" => "Strike off",
            "26" => "To Client",
            "27" => "Shelf",
            "28" => "Migrated to another agent",
            "29" => "Retire as Agent",
            "30" => "Retired",
            "31" => "in transfer to other agent",
            "32" => "Dissolved",
        ];

        return $statusesArray[$statusId] ?? 'In Active';
    }
    public function prepareContact($relationshipContacts, $contacts)
    {
        foreach ($relationshipContacts as $relationshipContact)
        {
            $contactDetails = collect($contacts)->firstWhere('ID', $relationshipContact['owner_id_reverse']);
            // Ensure contact details exist before creating the contact object
            if ($contactDetails) {
                $contactObject = $this->createContactObject('contact', $contactDetails, $relationshipContact);

                // Ensure the contact object is not null and has a 'relationship' key
                if ($contactObject && isset($contactObject['relationship'])) {
                    $relationshipName = strtolower($contactObject['relationship']);
                    $this->organizeRelationships('contact', $relationshipName, $contactObject);
                } else {
                    Log::error("Contact object is null or does not contain a 'relationship' key.", ['contactObject' => $contactObject]);
                }
            } else {
                Log::error("No contact found for owner_id_reverse: " . $relationshipContact['owner_id_reverse']);
            }
        }
    }
    public function prepareCompanyContact($relationshipCompanies, $companiesContacts)
    {
        foreach ($relationshipCompanies as $company)
        {
            $companyDetails = collect($companiesContacts)->firstWhere('ID', $company['owner_id_reverse']);
            $companyObject = $this->createCompanyObject($companyDetails, $company);
            $relationshipName = strtolower($company['name']);
            $this->organizeRelationships('company', $relationshipName, $companyObject);
        }
    }
    public function createContactObject($contactType, $contactDetails, $contact = null)
    {
        if($contactDetails)
        {
            $countryObj = collect($this->contactCountries)->firstWhere('ID', $contactDetails['UF_CRM_1621338479'] ?? 0);
            $placeOfBirthObj = collect($this->placeOfBirthCountries)->firstWhere('ID', $contactDetails['UF_CRM_1666854161'] ?? 0);
            $countryName = $countryObj ? ", " . $countryObj['VALUE'] : '';
            $address = trim(($contactDetails['UF_CRM_1539675920'] ?? '') . " " . ($contactDetails['UF_CRM_1539675930'] ?? '') . $countryName);

            if($contactType == 'contact'){
                return [
                    'id' => (int) $contactDetails['ID'],
                    'name' => $contactDetails['NAME'] . " " . $contactDetails['SECOND_NAME'] . " " . $contactDetails['LAST_NAME'],
                    'address' => $address !== '' ? trim($address) : 'Not Provided',
                    'dateOfBirth' => isset($contactDetails['BIRTHDATE']) && strtotime($contactDetails['BIRTHDATE']) ? date('d M Y', strtotime($contactDetails['BIRTHDATE'])) : 'Not Provided',
                    'nationality' => $contactDetails['UF_CRM_1539671799'] ?? 'Not Provided',
                    'placeOfBirth' => $placeOfBirthObj['VALUE'] ?? '',
                    'passportNumber' => $contactDetails['UF_CRM_1539671852'] ?? 'Not Provided',
                    'passportExpiry' => $contactDetails['UF_CRM_1539671987'] ?? 'Not Provided',
                    'startDate' => $contact['start_date'] ?? '',
                    'endDate' => $contact['end_date'] ?? '',
                    'notes' => $contact['notes'] ?? '',
                    'shares' => $contact['shares'] ?? '',
                    'nominee' => $contact['nominee_name'] ?? '',
                    'nomineeId' => isset($contact['nominee_id']) ? (int) $contact['nominee_id'] : '',
                    'nomineeType' =>  $contact['nominee_type'] ?? '',
                    'tin' => $contactDetails['UF_CRM_1539671927'] ?? 'Not Provided',
                    'type' => 'contact',
                    'typeId' => 3,
                    'relationship' => $contact['name'] ?? ''
                ];
            } else if ($contactType == 'nominee'){
                return [
                    'id' => (int) $contactDetails['ID'],
                    'name' => $contactDetails['NAME'] . " " . $contactDetails['SECOND_NAME'] . " " . $contactDetails['LAST_NAME'],
                    'address' => $address !== '' ? trim($address) : 'Not Provided',
                    'dateOfBirth' => isset($contactDetails['BIRTHDATE']) && strtotime($contactDetails['BIRTHDATE']) ? date('d M Y', strtotime($contactDetails['BIRTHDATE'])) : 'Not Provided',
                    'nationality' => $contactDetails['UF_CRM_1539671799'] ?? 'Not Provided',
                    'placeOfBirth' => $placeOfBirthObj['VALUE'] ?? '',
                    'passportNumber' => $contactDetails['UF_CRM_1539671852'] ?? 'Not Provided',
                    'passportExpiry' => $contactDetails['UF_CRM_1539671987'] ?? 'Not Provided',
                    'tin' => $contactDetails['UF_CRM_1539671927'] ?? 'Not Provided',
                    'type' => 'contact',
                    'typeId' => 3,
                ];
            }
        }
    }
    public function createCompanyObject($companyDetails, $company)
    {
        if($companyDetails)
        {
            $countryOfIncorporation = collect($this->companyIncorporationCountryList)->firstWhere('ID', $companyDetails['UF_CRM_1567589971'] ?? 0);
            return [
                'id' => (int) $companyDetails['ID'],
                'name' => $companyDetails['TITLE'],
                'address' => $companyDetails['UF_CRM_1567591803'] ?? 'Not Provided',
                'incorporationDate' => $companyDetails['UF_CRM_1540706070'] ? date('d M Y', strtotime($companyDetails['UF_CRM_1540706070'])) : 'Not Provided',
                'jurisdiction' => $countryOfIncorporation['VALUE'] ?? 'Not Provided',
                'licenseNumber' => $companyDetails['UF_CRM_1540705883'] ?? '',
                'tin' => $companyDetails['UF_CRM_1540705963'] ?? '',
                'startDate' => $company['start_date'] ?? '',
                'endDate' => $company['end_date'] ?? '',
                'notes' => $company['notes'] ?? '',
                'nominee' => $company['nominee_name'] ?? '',
                'nomineeId' => isset($company['nominee_id']) ? (int) $company['nominee_id'] : '',
                'nomineeType' => $company['nominee_type'] ?? '',
                'shares' => $company['shares'] ?? '',
                'type' => 'company',
                'typeId' => 4
            ];
        }
    }
    public function organizeRelationships($type, $relationship, $obj)
    {
        if($type == 'contact'){
            if (strpos($relationship, 'has individual director') !== false) {
                $this->individualDirectors[] = $obj;
            } else if (strpos($relationship, 'has individual secretary') !== false){
                $this->individualSecretaries[] = $obj;
            } else if (strpos($relationship, 'has individual shareholder') !== false){
                $obj['memberType'] = "shareholder";
                $this->shareholders[] = $obj;
            } else if (strpos($relationship, 'has guarantor member') !== false){
                $obj['memberType'] = "guarantee member";
                $this->shareholders[] = $obj;
            } else if (strpos($relationship, 'has ubo') !== false){
                $this->ubos[] = $obj;
            } else if (strpos($relationship, 'has nominee') !== false){
                // Check if $obj is an array or object
                $nomineeId = is_array($obj) ? $obj['nomineeId'] ?? null : $obj->nomineeId;
                $nomineeType = is_array($obj) ? $obj['nomineeType'] ?? null : $obj->nomineeType;

                if ($nomineeId) {
                    // Contact nominator
                    if ($nomineeType == 3) {
                        $contactNominator =  $this->bitrixRepository->getContactNominator($nomineeId);
                        $obj['nominator'] = $this->createContactObject('nominee', $contactNominator, null);
                        $this->nominees[] = $obj;
                    } else if($nomineeType == 4){
                        $companyNominator =  $this->bitrixRepository->getCompanyNominator($nomineeId);
                        $obj['nominator'] = $this->createCompanyNominatorObject($companyNominator);
                        $this->nominees[] = $obj;
                    }
                } else {
                    $this->nominees[] = $obj;
                }
            } else if (strpos($relationship, 'has founder') !== false){
                $this->founders[] = $obj;
            } else if (strpos($relationship, 'beneficiary') !== false && strpos($relationship, 'has') !== false){
                $this->beneficiaries[] = $obj;
            } else if (strpos($relationship, 'has protector') !== false ){
                $this->protectors[] = $obj;
            } else if (strpos($relationship, 'has individual council member') !== false ){
                $this->councilors[] = $obj;
            } else if (strpos($relationship, 'has authorized person') !== false ){
                $this->authorizedPersons[] = $obj;
            } else if (strpos($relationship, 'has officer') !== false ){
                $this->officers[] = $obj;
            }
        }
        if($type == 'company'){
            if (strpos($relationship, 'has corporate director') !== false) {
                $this->individualDirectors[] = $obj;
            } else if (strpos($relationship, 'has corporate secretary') !== false){
                $this->corporateSecretaries[] = $obj;
            } else if (strpos($relationship, 'has corporate shareholder') !== false){
                $obj['memberType'] = "shareholder";
                $this->shareholders[] = $obj;
            } else if (strpos($relationship, 'has guarantor member') !== false){
                $obj['memberType'] = "guarantee member";
                $this->shareholders[] = $obj;
            } else if (strpos($relationship, 'has nominee') !== false){
                // Check if $obj is an array or object
                $nomineeId = is_array($obj) ? $obj['nomineeId'] ?? null : $obj->nomineeId;
                $nomineeType = is_array($obj) ? $obj['nomineeType'] ?? null : $obj->nomineeType;

                if ($nomineeId) {
                    // Contact nominator
                    if ($nomineeType == 3) {
                        $contactNominator =  $this->bitrixRepository->getContactNominator($nomineeId);
                        $obj['nominator'] = $this->createContactObject('nominee', $contactNominator, null);
                        $this->nominees[] = $obj;
                    } else if($nomineeType == 4){
                        $companyNominator =  $this->bitrixRepository->getCompanyNominator($nomineeId);
                        $obj['nominator'] = $this->createCompanyNominatorObject($companyNominator);
                        $this->nominees[] = $obj;
                    }
                } else {
                    $this->nominees[] = $obj;
                }
            }
        }

    }
    public function createCompanyNominatorObject($companyNominator)
    {
        if($companyNominator)
        {
            $countryOfIncorporation = collect($this->companyIncorporationCountryList)->firstWhere('ID', $companyNominator->UF_CRM_1567589971);

            return [
                'id' => (int) $companyNominator['ID'],
                'name' => $companyNominator['TITLE'],
                'address' => $companyNominator['UF_CRM_1567591803'] ?? 'Not Provided',
                'incorporationDate' => isset($companyNominator['UF_CRM_1540706070']) ? date('d M Y', strtotime($companyNominator['UF_CRM_1540706070'])) : 'Not Provided',
                'jurisdiction' => $countryOfIncorporation['VALUE'] ?? 'Not Provided',
                'licenseNumber' => $companyNominator['UF_CRM_1540705883'],
                'tin' => $companyNominator['UF_CRM_1540705963'],
                'type' => 'company',
                'typeId' => 4
            ];
        }
    }
    private function sortByDate(&$array, $dateKey)
    {
        usort($array, function ($a, $b) use ($dateKey) {
            $dateA = isset($a[$dateKey]) ? strtotime($a[$dateKey]) : 0;
            $dateB = isset($b[$dateKey]) ? strtotime($b[$dateKey]) : 0;

            return $dateA <=> $dateB; // Ascending order
        });
    }
    public function getCompanyOfIncorporation($incorporationId)
    {
        $obj = collect($this->companyIncorporationCountryList)->firstWhere('ID', $incorporationId ?? 0);
        return $obj['VALUE'];
    }
    public function makeFounderData($company, $relationships)
    {
        $founders = $relationships['founders'];
        if(is_array($founders) && count($founders) > 0){
            $founderContactIds = collect($founders)->pluck('id');
            $founderRoles = $this->getRelationshipRoles($company['company_id'], $founderContactIds);
            if($founderRoles->isNotEmpty()){
                // Iterate over each founder
                foreach ($founders as &$founder) {
                    // Use collection's first() method to find the matching object where contact_id matches the founder's id
                    $foundObject = $founderRoles->first(function ($role) use ($founder) {
                        return (int) $role['contact_id'] === (int) $founder['id'];
                    });

                    // If found, assign the role to the founder
                    if ($foundObject) {
                        $founder['role'] = $foundObject['role'];
                    }
                }
            }
            return $founders;
        }
        Log::info("No Founders found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function getRelationshipRoles($companyId, $contactIds)
    {
        return $this->bitrixRepository->getRelationshipRoles($companyId, $contactIds);
    }
    public function makeBeneficiariesData($company, $relationships)
    {
        $beneficiaries = $relationships['beneficiaries'];
        if($beneficiaries){
            // Define the position order for beneficiaries
            $positionOrder = [
                "has first beneficiary" => 1,
                "has second beneficiary" => 2,
                "has third beneficiary" => 3,
                "has fourth beneficiary" => 4,
                "has fifth beneficiary" => 5,
                "has sixth beneficiary" => 6
            ];

            // Sort the beneficiaries based on the 'relationship' key
            usort($beneficiaries, function ($a, $b) use ($positionOrder) {
                return $positionOrder[$a['relationship']] - $positionOrder[$b['relationship']];
            });
            return $beneficiaries;
        }
        Log::info("No Beneficiaries found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function makeProtectorsData($company, $relationships)
    {
        $protectors = $relationships['protectors'];
        if($protectors) {
            $this->sortByDate($protectors, 'startDate');
            return $protectors;
        }
        Log::info("No Protectors found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function makeCouncilorsData($company, $relationships)
    {
        $councilors = $relationships['councilors'];

        if(is_array($councilors) && count($councilors) > 0){
            $councilorContactIds = collect($councilors)->pluck('id');
            $councilorRoles = $this->getRelationshipRoles($company['company_id'], $councilorContactIds);
            if($councilorRoles->isNotEmpty()){
                // Iterate over each founder
                foreach ($councilors as &$councilor) {
                    // Use collection's first() method to find the matching object where contact_id matches the founder's id
                    $foundObject = $councilorRoles->first(function ($role) use ($councilor) {
                        return (int) $role['contact_id'] === (int) $councilor['id'];
                    });

                    // If found, assign the role to the founder
                    if ($foundObject) {
                        $councilor['role'] = $foundObject['role'];
                    }
                }
            }
            return $councilors;
        }
        Log::info("No Councilors found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function makeAuthorizedPersonsData($company, $relationships)
    {
        $authorizedPersons = $relationships['authorizedPersons'];
        if(is_array($authorizedPersons) && count($authorizedPersons) > 0){
            $this->sortByDate($authorizedPersons, 'startDate');
            return $authorizedPersons;
        }
        Log::info("No Authorized Persons found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function makeOfficersData($company, $relationships)
    {
        $officers = $relationships['officers'];

        if(is_array($officers) && count($officers) > 0){
            $officerContactIds = collect($officers)->pluck('id');

            $officerRoles = $this->getRelationshipRoles($company['company_id'], $officerContactIds);
            if($officerRoles->isNotEmpty()){
                // Iterate over each founder
                foreach ($officers as &$officer) {
                    // Use collection's first() method to find the matching object where contact_id matches the founder's id
                    $foundObject = $officerRoles->first(function ($role) use ($officer) {
                        return (int) $role['contact_id'] === (int) $officer['id'];
                    });

                    // If found, assign the role to the founder
                    if ($foundObject) {
                        $officer['role'] = $foundObject['role'];
                    }
                }
            }
            return $officers;
        }
        Log::info("No Officers found for company: {$company['company_name']} (ID: {$company['company_id']})");
    }
    public function documentFields()
    {
        return [
            'UF_CRM_1705913894', // VAT
            'UF_CRM_1705914755', // Tax Documents
            'UF_CRM_1706253922', // Tax Opinion
            'UF_CRM_1656500171823', // 2015 Accounting Documents
            'UF_CRM_1656500253654', // 2016 Accounting Documents
            'UF_CRM_1656316012510', // 2017 Accounting Documents
            'UF_CRM_1656316043662', // 2018 Accounting Documents
            'UF_CRM_1656316101454', // 2019 Accounting Documents
            'UF_CRM_1656316217175', // 2020 Accounting Documents
            'UF_CRM_1656316239910', // 2021 Accounting Documents
            'UF_CRM_1656500360289', // 2022 Accounting Documents
            'UF_CRM_1718110098680', // 2023 Accounting Documents
            'UF_CRM_1718110314900', // 2024 Accounting Documents
            'UF_CRM_1739779632129', // 2025 Accounting Documents
            'UF_CRM_1658735216676', // HC Contract
            'UF_CRM_1655971554391', // Certificate of Incorporation
            'UF_CRM_1674194878', // Certificate of Incumbency
            'UF_CRM_1655971481119', // Memorandum of Association
            'UF_CRM_1656073208627', // Yearly License (single)
            'UF_CRM_1728288798261', // Establishment Card
            'UF_CRM_1655902818844', // UBO declaration
            'UF_CRM_1655902891145', // UBO portal registration
            'UF_CRM_1683095852519', // KYC Package
            'UF_CRM_1712057271', // Company Stamp (single)
            'UF_CRM_1682422207958', // Company Logo
            'UF_CRM_1682422257725', // Company Letterhead
            'UF_CRM_1682503229744', // Business Card Design
        ];
    }
    public function contactDocumentFields()
    {
        return [
            'UF_CRM_1656067869643', // Passport (single)
            'UF_CRM_1707806794', // UAE Residence VISA File (single)
            'UF_CRM_1675085070282', // Emirates ID Scan (single)
            'UF_CRM_1656067944685', // Birth Certificate (single)
            'UF_CRM_1656068064883', // Marriage Certificate (single)
            'UF_CRM_1547529289902', // Passport Transmittal RCPT (single)
            'UF_CRM_1712312361019', // CV (single)
        ];
    }
    public function prepareCompanyRegisterData($company, $companyData, $relationships)
    {
        return [
            'company_name' => $company['company_name'],
            'registry_number' => $company['s_license_no'] ?? 'Not Provided',
            'incorporation_date' => $company['incorporation_date'] ?? 'Not Provided',
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            'directors' => $relationships['individualDirectors'],
            "secretaries" => $relationships['individualSecretaries'],
            'shareholders' => $relationships['shareholders'],
            "ubos" => $relationships['ubos'],
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareFoundersData($company, $companyData, $founders)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "founders" => $founders,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareBeneficiariesData($company, $companyData, $beneficiaries)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "beneficiaries" => $beneficiaries,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareProtectorsData($company, $companyData, $protectors)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "protectors" => $protectors,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareCouncilorsData($company, $companyData, $councilorsData)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "councilors" => $councilorsData,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareAuthorizedPersonsData($company, $companyData, $authorizedPersons)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "authorized_persons" => $authorizedPersons,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
    public function prepareOfficersData($company, $companyData, $officers)
    {
        return [
            "company_name" => $company['company_name'],
            "registry_number" => $company['s_license_no'] ?? 'Not Provided',
            "incorporation_date" =>  date("d M Y", strtotime($company['incorporation_date'])),
            "organization_type" => $this->getOrganizationType($company['organization_type']) ?? 'Not Provided',
            "authority" => $company['authority'] ?? 'Not Provided',
            "registered_address" => $companyData['UF_CRM_1567591803'] ?? 'No Registered Address',
            "authorized_persons" => $officers,
            "country_of_incorporation" => $this->getCompanyOfIncorporation($companyData['UF_CRM_1567589971']),
            "status" => $this->getHensleyAndCookStatusText($company['company_status']) ?? 'In Active',
            "tin" => $company['tin'] ?? 'Not Provided',
        ];
    }
}
