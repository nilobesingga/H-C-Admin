<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use App\Models\ContactRelationship;
use App\Models\CompanyBankAccount;
use App\Models\CompanyContact;
use App\Models\CompanyDocument;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Sync contact data with the provided request.
     */
    public function syncContact(Request $request)
    {
        // DB::beginTransaction();
        try {
            // Process companies
            $this->storeContact($request->all());
            $user = User::where('bitrix_contact_id', $request->contact_id)->first();
            $modules = DB::table('modules')
                        ->get();
            DB::table('user_module_permission')
                ->where('user_id', $user->id)
                ->delete();
            foreach ($modules as $module) {
                DB::table('user_module_permission')->insert([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'permission' => 'full_access', // Default permission
                    'created_by' => $user->id,
                    'created_at' => now(),
                ]);
            }
            if ($request->has('companyData') && is_array($request->companyData)) {
                DB::table('company_contact')
                ->where('contact_id', $request->contact_id)
                ->delete();
                foreach ($request->companyData as $company) {
                    CompanyContact::updateOrCreate(
                        [
                            'company_id' => $company['company_id'],
                            'contact_id' => $request->contact_id,
                        ],
                        [
                            'position' => $company['position'] ?? null,
                            'department' => $company['department'] ?? null,
                            'role' => $company['role'] ?? null,
                            'start_date' => isset($company['start_date']) ? date('Y-m-d', strtotime($company['start_date'])) : null,
                            'end_date' => isset($company['end_date']) ? date('Y-m-d', strtotime($company['end_date'])) : null,
                            'is_primary' => $company['is_primary'] ?? false,
                            'notes' => $company['notes'] ?? null,
                        ]
                    );
                    // Store company data
                    $this->storeCompany($company);
                    $this->storeRelation($company['relation'] ?? []);
                    $this->storeBankAccounts($company['banks'] ?? [], $company['company_id'] ?? null);
                    // Handle file uploads if any
                    $uploadedDocuments = [];
                    if(isset($company['attachments']) && is_array($company['attachments'])) {
                        foreach ($company['attachments'] as $key => $files) {
                            $uploadedDocuments[$key][] = ['file' => $files];
                        }
                        // If we have uploaded files, process them
                        if (!empty($uploadedDocuments)) {
                            $this->storeCompanyDocuments($uploadedDocuments, $company['company_id']);
                        }
                    }
                }
            }
            // DB::commit();
            return response()->json(['success' => true,'message' => 'Data synced successfully.'], 200);
        } catch (\Throwable $th) {
            // DB::rollBack();
            // throw $th;
            return response()->json(['success' => false, 'message' => 'Failed to sync data: ' . $th->getMessage()], 500);

        }
    }
    public function storeCompany($company)
    {
        DB::beginTransaction();
        $validator = Validator::make($company, [
            'company_id' => 'required|string',
            'name' => 'required|string',
            'license_number' => 'nullable|string',
            'incorporation_date' => 'nullable',
            // 'license_expiry_date' => 'nullable|date',
            'authority' => 'nullable|string',
            'organization_type' => 'nullable|string',
            'status' => 'nullable|string',
            'company_activity' => 'nullable|string',
            'website' => 'nullable',
            'email' => 'nullable|email',
            'contact_no' => 'nullable|string',
            'registered_address' => 'nullable|string',
            'office_no' => 'nullable|string',
            'building_name' => 'nullable|string',
            'po_box' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'annual_turnover' => 'nullable',
            // Add validation for logo if needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Company::updateOrCreate(
                ['company_id' => $company['company_id']],
                $company
            );
            DB::commit();
            return response()->json(['message' => 'Company synced successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function storeRelation($relation)
    {
        $validator = Validator::make($relation, [
            'tec_custom_field_id' => 'nullable|string',
            'owner_id' => 'nullable|string',
            'owner_type' => 'nullable|string',
            'owner' => 'nullable|string',
            'name' => 'nullable|string',
            'owner_reverse' => 'nullable|string',
            'owner_id_reverse' => 'nullable|string',
            'owner_type_reverse' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'shares' => 'nullable|string',
            'nominee_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            foreach($relation as $key => $value) {
                ContactRelationship::updateOrCreate(
                    [
                        'tec_custom_field_id' => $value['tec_custom_field_id'],
                        'owner_id' => $value['owner_id'],
                    ],
                    $value
                );
            }
            return response()->json(['message' => 'Contact relationship synced successfully.']);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Failed to sync contact relationship: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store bank accounts for a company.
     *
     * @param array $banks
     * @return void
     */
    public function storeBankAccounts($banks, $companyId)
    {
        if (empty($banks)) {
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($banks as $bank) {
                $validator = Validator::make($bank, [
                    'company_id' => 'required',
                    'name' => 'nullable|string',
                    'account_number' => 'nullable|string',
                    'iban' => 'nullable|string',
                    'swift' => 'nullable|string',
                    'currency' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    throw new \Exception('Bank account validation failed: ' . json_encode($validator->errors()));
                }

                CompanyBankAccount::updateOrCreate(
                    [
                        'company_id' => $bank['company_id'],
                        'account_number' => isset($bank['account_number']) ? $bank['account_number'] : null,
                        'currency' => isset($bank['currency']) ? $bank['currency'] : null,
                    ],
                    [
                        'company_id' => $bank['company_id'],
                        'name' => isset($bank['name']) ? $bank['name'] : null,
                        'account_number' => isset($bank['account_number']) ? $bank['account_number'] : null,
                        'display_text' => isset($bank['display_text']) ? $bank['display_text'] : null,
                        'iban' => isset($bank['iban']) ? $bank['iban'] : null,
                        'swift' => isset($bank['swift']) ? $bank['swift'] : null,
                        'currency' => isset($bank['currency']) ? $bank['currency'] : null,
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store company documents.
     *
     * @param array $documents An array of document data by type
     * @param string $companyId The company ID to associate documents with
     * @return void
     */
    public function storeCompanyDocuments($documents, $companyId)
    {
        if (empty($documents)) {
            return;
        }

        DB::beginTransaction();

        try {
            // Process each document type
            foreach ($documents as $type => $items) {
                // Skip empty document types
                if (empty($items)) {
                    continue;
                }
                // For other document types, we have a direct array of documents
                foreach ($items as $document) {
                    $this->processDocument($document, $companyId, $type);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process an individual document
     *
     * @param array|UploadedFile $document Document data or uploaded file
     * @param string $companyId Company ID
     * @param string $type Document type
     * @param string|null $year Document year (for accounting documents)
     * @return void
     */
    private function processDocument($document, $companyId, $type, $year = null)
    {
        foreach ($document['file'] as $fileData) {
            if (isset($fileData['files']) && is_array($fileData['files'])) {
                // Process file data from structured upload
                $fileInfo = $fileData['files'];
                $filename = $fileData['filename'] ?? $fileInfo['postname'] ?? null;
                $year = $fileData['year'] ?? null;
                if (!$filename) {
                    throw new \Exception('Filename is required for document processing');
                }

                // Handle temp file if available
                $tempFilePath = $fileInfo['name'] ?? null;
                if ($tempFilePath && file_exists($tempFilePath)) {                        // Define storage path
                    $storagePath = "company_documents/{$companyId}/{$type}";
                    if ($year) {
                        $storagePath .= "/{$year}";
                    }

                    // Generate safe filename
                    $safeName = time() . '_' . Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

                    // Check if file already exists and delete it to allow overwriting
                    $fullPath = "public/{$storagePath}/{$safeName}";
                    if (Storage::exists($fullPath)) {
                        Storage::delete($fullPath);
                    }

                    // Store the file from temp location, overwriting if it exists
                    $path = Storage::disk('public')->putFileAs($storagePath, $tempFilePath, $safeName);

                    if (!$path) {
                        throw new \Exception("Failed to store file: {$filename}");
                    }

                    // Create document record
                    CompanyDocument::updateOrCreate(
                        [
                            'company_id' => $companyId,
                            'type' => $type,
                            'year' => $year,
                            'filename' => $filename,
                        ],
                        [
                            'path' => $path,
                            'url' => Storage::url($path),
                            // 'status' => 'active',
                        ]
                    );
                } else {
                    // Just store metadata if no temp file
                    CompanyDocument::updateOrCreate(
                        [
                            'company_id' => $companyId,
                            'type' => $type,
                            'year' => $year,
                            'filename' => $filename,
                        ],
                        [
                            'status' => 'active',
                        ]
                    );
                }
            }
        }
    }

    /**
     * Upload a document for a company.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyDocument(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'company_id' => 'required|exists:companies,company_id',
                'type' => 'required|string',
                'year' => 'nullable|string',
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Prepare document data
            $documentData = [
                'file' => $request->file('files'),
            ];

            // Process the document
            $this->processDocument(
                $documentData,
                $request->input('company_id'),
                $request->input('type'),
                $request->input('year')
            );

            DB::commit();

            return response()->json([
                'message' => 'Document uploaded successfully.',
                'type' => $request->input('type'),
                'year' => $request->input('year'),
                'filename' => $request->file('file')->getClientOriginalName(),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            // return response()->json(['error' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store contact data.
     *
     * @param array $contact Contact data
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeContact($contact)
    {
        DB::beginTransaction();

        $validator = Validator::make($contact, [
            'contact_id' => 'required|string',
            'name' => 'required|string',
            'nationality' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'phone_no' => 'nullable|string',
            'tin' => 'nullable|string',
            'passport_number' => 'nullable|string',
            'passport_place_of_issue' => 'nullable|string',
            'passport_issue_date' => 'nullable|date',
            'passport_expiry_date' => 'nullable|date',
            'residence_visa_number' => 'nullable|string',
            'residence_visa_file_number' => 'nullable|string',
            'residence_visa_issue_date' => 'nullable|date',
            'residence_visa_expiry_date' => 'nullable|date',
            'emirates_id_number' => 'nullable|string',
            'emirates_id_issue_date' => 'nullable|date',
            'emirates_id_expiry_date' => 'nullable|date',
            'address' => 'nullable|string',
            'email' => 'nullable|string',
            // Add validation for file fields if needed
            'cv_file' => 'nullable|string',
            'photo' => 'nullable',
            'passport_file' => 'nullable|string',
            'residence_visa_file' => 'nullable|string',
            'emirates_id_file' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {            // Process file uploads if any
            $contactData = $contact;
            // Handle contact files (photo, passport, emirates ID, visa)
            if (isset($contact['contactFiles']) && is_array($contact['contactFiles'])) {
                // Process photo files
                if (isset($contact['contactFiles']['photo'])) {
                    $photoData = $this->processContactFileArray($contact['contactFiles']['photo'], $contact['contact_id'], 'photo');
                    if ($photoData) {
                        $contactData['photo'] = $photoData;
                    }
                }

                // Process passport files
                if (isset($contact['contactFiles']['passport'])) {
                    $passportData = $this->processContactFileArray($contact['contactFiles']['passport'], $contact['contact_id'], 'passport');
                    if ($passportData) {
                        $contactData['passport_file'] = $passportData;
                    }
                }

                // Process Emirates ID files
                if (isset($contact['contactFiles']['emirates'])) {
                    $emiratesData = $this->processContactFileArray($contact['contactFiles']['emirates'], $contact['contact_id'], 'emirate');
                    if ($emiratesData) {
                        $contactData['emirates_id_file'] = $emiratesData;
                    }
                }

                // Process visa files
                if (isset($contact['contactFiles']['visa'])) {
                    $visaData = $this->processContactFileArray($contact['contactFiles']['visa'], $contact['contact_id'], 'visa');
                    if ($visaData) {
                        $contactData['residence_visa_file'] = $visaData;
                    }
                }

                // Process cv files
                if (isset($contact['contactFiles']['cv'])) {
                    $cvData = $this->processContactFileArray($contact['contactFiles']['cv'], $contact['contact_id'], 'cv');
                    if ($cvData) {
                        $contactData['cv_file'] = $cvData;
                    }
                }
            }

            // Create or update the contact
            $contactModel = Contact::updateOrCreate(
                ['contact_id' => $contact['contact_id']],
                $contactData
            );

            // Handle company associations if provided
            // if (!empty($contact['companyData'])) {
            //     $this->attachContactToCompanies($contactModel, $contact['companyData']);
            // }

            DB::commit();
            return response()->json(['message' => 'Contact stored successfully.', 'contact' => $contactModel], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            throw $e;
            // Log::["message" => 'Failed to store contact: ' . $e->getMessage(),
            //     'contact' => $contact,
            //     'exception' => $e,
            // ]);
            // return response()->json(['error' => 'Failed to store contact: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Process an array of contact files (photos, passports, etc.)
     *
     * @param array $fileArray Array of file data from the request
     * @param string $contactId Contact ID to associate the file with
     * @param string $fileType Type of file (photos, passports, etc.)
     * @return array|null File metadata of the first file or null if processing failed
     */
    private function processContactFileArray($document, $contactId, $fileType)
    {
        if (!is_array($document) || empty($document)) {
            return null;
        }
        foreach ($document as $fileData) {
            if (isset($fileData['files']) && is_array($fileData['files'])) {
                // Process file data from structured upload
                $fileInfo = $fileData['files'];
                $filename = $fileData['filename'] ?? $fileInfo['postname'] ?? null;
                if (!$filename) {
                    throw new \Exception('Filename is required for document processing');
                }
                // Handle temp file if available
                $tempFilePath = $fileInfo['name'] ?? null;
                if ($tempFilePath && file_exists($tempFilePath)) {                        // Define storage path
                    $storagePath = "contact_documents/{$contactId}/{$fileType}";
                    // Generate safe filename
                    $safeName = time() . '_' . Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

                    // Check if file already exists and delete it to allow overwriting
                    $fullPath = "public/{$storagePath}/{$safeName}";
                    if (Storage::exists($fullPath)) {
                        Storage::delete($fullPath);
                    }

                    // Store the file from temp location, overwriting if it exists
                    return Storage::disk('public')->putFileAs($storagePath, $tempFilePath, $safeName);

                    if (!$path) {
                        throw new \Exception("Failed to store file: {$filename}");
                    }
                }
            }
        }
    }

    public function getContactCompany($contactId){
        try {
            $contacts = CompanyContact::select('company_id')->where('contact_id', $contactId)
                        ->get()->pluck('company_id');
            return response()->json(['status' => true, 'data' => $contacts], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to retrieve contacts: ' . $e->getMessage()], 500);
        }
    }
}
