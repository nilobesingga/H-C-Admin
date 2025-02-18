<?php

namespace App\Http\Controllers;

use App\Jobs\FSASyncDocumentsJob;
use App\Services\DocumentService\DocumentManagerService;
use App\Services\DocumentService\DocumentSyncService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DocumentSyncController extends Controller
{
    public $bitrixService;
    public $documentManager;

    public function __construct(DocumentSyncService $bitrixService, DocumentManagerService $documentManager)
    {
        $this->bitrixService = $bitrixService;
        $this->documentManager = $documentManager;
    }
    public function syncFSADocuments()
    {
        FSASyncDocumentsJob::dispatch();
        return response()->json(['message' => 'Sync started'], 200);

//        try {
//            set_time_limit(3600);
//            ini_set('memory_limit', '512M');
//            Log::info("Document sync process initiated.");
//            $companies = $this->bitrixService->getHostedCompanies();
//            $companiesCount =  1;
////            Bali Foundation
//             $testCompanies = $companies->where('company_id', '3274118')->toArray();
//
//            $count = 1;
//            $companiesArray = $companies->toArray();
//            $companyChunks = array_chunk($testCompanies, 10);
//
//            // Initialize progress in the cache (or session)
//            Cache::put('sync_progress', 0, 30);
//
//            foreach ($companyChunks as $chunk){
//                foreach ($chunk as $company) {
//                    try {
//                        $companyId = $company['company_id'];
//                        Log::info("***** Processing Company ID: {$companyId} ----------- Company Name: {$company['company_name']} ---------- (" . ($count++) . " of {$companiesCount}) -------------------------");
//                        $companyData = $this->bitrixService->getCompanyData($companyId);
//                        if ($companyData) {
//                            $documents = $this->documentManager->filterUploadedDocuments($companyData, $this->bitrixService->documentFields());
//                            Log::info('getting company relationships for company id: ' . $companyId);
//                            // Relationships
//                            $relationships = $this->bitrixService->getCompanyRelationships($companyId);
//                            $contacts = $relationships['contacts'];
//                            $companyRegisterData = $this->bitrixService->prepareCompanyRegisterData($company, $companyData, $relationships);
//                            $this->documentManager->downloadCompanyDocuments($company, $documents, $companyRegisterData, $contacts, $this->bitrixService->contactDocumentFields());
//                            // Founders
//                            $founders = $this->bitrixService->makeFounderData($company, $relationships);
//                            if($founders){
//                                $foundersData = $this->bitrixService->prepareFoundersData($company, $companyData, $founders);
//                                $this->documentManager->downloadFounderDocument($company, $foundersData);
//                            }
//                            // Beneficiaries
//                            $beneficiaries = $this->bitrixService->makeBeneficiariesData($company, $relationships);
//                            if($beneficiaries){
//                                $beneficiariesData = $this->bitrixService->prepareBeneficiariesData($company, $companyData, $beneficiaries);
//                                $this->documentManager->downloadBeneficiaryDocument($company, $beneficiariesData);
//                            }
//                            // Protectors
//                            $protectors = $this->bitrixService->makeProtectorsData($company, $relationships);
//                            if($protectors){
//                                $protectorsData = $this->bitrixService->prepareProtectorsData($company, $companyData, $protectors);
//                                $this->documentManager->downloadProtectorDocument($company, $protectorsData);
//                            }
//                            // Councilors
//                            $councilors = $this->bitrixService->makeCouncilorsData($company, $relationships);
//                            if($councilors){
//                                $councilorsData = $this->bitrixService->prepareCouncilorsData($company, $companyData, $councilors);
//                                $this->documentManager->downloadCouncilorDocument($company, $councilorsData);
//                            }
//                            // Authorized Persons
//                            $authorizedPersons = $this->bitrixService->makeAuthorizedPersonsData($company, $relationships);
//                            if($authorizedPersons){
//                                $authorizedPersonsData = $this->bitrixService->prepareAuthorizedPersonsData($company, $companyData, $authorizedPersons);
//                                $this->documentManager->downloadAuthorizedPersonDocument($company, $authorizedPersonsData);
//                            }
//                            // Officers
//                            $officers = $this->bitrixService->makeOfficersData($company, $relationships);
//                            if($officers){
//                                $officersData = $this->bitrixService->prepareOfficersData($company, $companyData, $officers);
//                                $this->documentManager->downloadOfficersDocument($company, $officersData);
//                            }
//                        }
//
//                        // Update progress in cache
//                        $progress = ($count / $companiesCount) * 100;
//                        Cache::put('sync_progress', $progress, 30);  // Store progress percentage in the cache
//
//                        unset($companyData, $documents, $relationships, $companyRegisterData);
//                        gc_collect_cycles();
//
//                    } catch (\Exception $e){
//                        Log::error("Error processing company {$company['company_name']} (ID: {$companyId}): " . $e->getMessage(), [
//                            'stack_trace' => $e->getTraceAsString()
//                        ]);
//                    }
//                }
//            }
//
//            Log::info("Document sync process completed");
//            Cache::forget('sync_progress');
//            return response()->json('Documents synced successfully.', 200);
//
//        } catch (\Exception $e){
//            Log::error('An error occurred during document sync', [
//                'exception' => $e->getMessage(),
//                'stack' => $e->getTraceAsString(),
//            ]);
//            Cache::forget('sync_progress');
//
//            // Return a generic error response
//            return response()->json([
//                'error' => 'An unexpected error occurred while fetching bitrix companies',
//                'details' => $e->getMessage(),
//            ], 500);
//        }
    }
    public function getSyncFSADocumentsProgress()
    {
        $progress = Cache::get('sync_progress');
        return response()->json(['progress' => $progress]);
    }
}
