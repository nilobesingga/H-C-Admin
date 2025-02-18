<?php

namespace App\Jobs;

use App\Services\DocumentService\DocumentManagerService;
use App\Services\DocumentService\DocumentSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FSASyncDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Resolve the services inside the handle method
            $documentSyncService = app(DocumentSyncService::class);
            $documentManager = app(DocumentManagerService::class);

            set_time_limit(3600);
            ini_set('memory_limit', '512M');
            Log::info("Document sync process initiated.");
            $companies = $documentSyncService->getHostedCompanies();
            $companiesCount =  count($companies);

            $count = 1;
            $companiesArray = $companies->toArray();
            $companyChunks = array_chunk($companiesArray, 10);

            // Initialize progress in the cache (or session)
            Cache::put('sync_progress', 0); // or session()->put('sync_progress', 0);

            foreach ($companyChunks as $chunk){
                foreach ($chunk as $company) {
                    try {
                        $companyId = $company['company_id'];
                        Log::info("***** Processing Company ID: {$companyId} ----------- Company Name: {$company['company_name']} ---------- (" . ($count++) . " of {$companiesCount}) -------------------------");
                        $companyData = $documentSyncService->getCompanyData($companyId);

                        if ($companyData) {
                            $documents = $documentManager->filterUploadedDocuments($companyData, $documentSyncService->documentFields());
                            Log::info('getting company relationships for company id: ' . $companyId);
                            // Relationships
                            $relationships = $documentSyncService->getCompanyRelationships($companyId);
                            $companyRegisterData = $documentSyncService->prepareCompanyRegisterData($company, $companyData, $relationships);
                            $documentManager->downloadCompanyDocuments($company, $documents, $companyRegisterData);
                            // Founders
                            $founders = $documentSyncService->makeFounderData($company, $relationships);
                            if($founders){
                                $foundersData = $documentSyncService->prepareFoundersData($company, $companyData, $founders);
                                $documentManager->downloadFounderDocument($company, $foundersData);
                            }
                            // Beneficiaries
                            $beneficiaries = $documentSyncService->makeBeneficiariesData($company, $relationships);
                            if($beneficiaries){
                                $beneficiariesData = $documentSyncService->prepareBeneficiariesData($company, $companyData, $beneficiaries);
                                $documentManager->downloadBeneficiaryDocument($company, $beneficiariesData);
                            }
                            // Protectors
                            $protectors = $documentSyncService->makeProtectorsData($company, $relationships);
                            if($protectors){
                                $protectorsData = $documentSyncService->prepareProtectorsData($company, $companyData, $protectors);
                                $documentManager->downloadProtectorDocument($company, $protectorsData);
                            }
                            // Councilors
                            $councilors = $documentSyncService->makeCouncilorsData($company, $relationships);
                            if($councilors){
                                $councilorsData = $documentSyncService->prepareCouncilorsData($company, $companyData, $councilors);
                                $documentManager->downloadCouncilorDocument($company, $councilorsData);
                            }
                            // Authorized Persons
                            $authorizedPersons = $documentSyncService->makeAuthorizedPersonsData($company, $relationships);
                            if($authorizedPersons){
                                $authorizedPersonsData = $documentSyncService->prepareAuthorizedPersonsData($company, $companyData, $authorizedPersons);
                                $documentManager->downloadAuthorizedPersonDocument($company, $authorizedPersonsData);
                            }
                            // Officers
                            $officers = $documentSyncService->makeOfficersData($company, $relationships);
                            if($officers){
                                $officersData = $documentSyncService->prepareOfficersData($company, $companyData, $officers);
                                $documentManager->downloadOfficersDocument($company, $officersData);
                            }
                        }

                        // Update progress in cache
                        $progress = round(($count / $companiesCount) * 100);
                        Cache::put('sync_progress', $progress);  // Store progress percentage in the cache

                        unset($companyData, $documents, $relationships, $companyRegisterData);
                        gc_collect_cycles();

                    } catch (\Exception $e){
                        Log::error("Error processing company {$company['company_name']} (ID: {$companyId}): " . $e->getMessage(), [
                            'stack_trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            }

            Cache::forget('sync_progress');
            Log::info("Document sync process completed");

        } catch (\Exception $e){
            Log::error('An error occurred during document sync', [
                'exception' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            Cache::forget('sync_progress');
        }
    }
}
