<?php

namespace App\Services\DocumentService;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf as WPDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentManagerService
{
    public function filterUploadedDocuments($companyData, $documentFields)
    {
        Log::info('DocumentManagerService: filterUploadedDocuments | Filtering documents.....');
        $filteredDocuments = [];
        foreach ($documentFields as $field) {
            // Check if the field exists in companyData
            if (isset($companyData[$field])) {
                // Handle the specific case of 'UF_CRM_1656073208627' without flattening
                if ($field === 'UF_CRM_1656073208627' || $field === 'UF_CRM_1712057271') {
                    // Directly push this document into the array as it has only one document
                    $filteredDocuments[] = $companyData[$field];
                } else {
                    // For other fields, check if the value is an array (which may contain nested arrays)
                    if (is_array($companyData[$field]) && !empty($companyData[$field])) {
                        // Flatten only the nested arrays
                        foreach ($companyData[$field] as $document) {
                            // Each document array gets added to the result
                            $filteredDocuments[] = $document;
                        }
                    }
                }
            }
        }
        return $filteredDocuments;
    }
    public function downloadCompanyDocuments($company, $documents, $companyRegisterData, $contacts, $contactDocumentFields)
    {
        $companyName = $company['company_name'];

        $documentChunks = array_chunk($documents, 10);
        foreach ($documentChunks as $chunk) {
            $this->downloadAndSaveDocument($companyName, $chunk);
        }
        $this->saveCompanyRegister($companyName, $companyRegisterData);
        $this->downloadAndSaveComapnyContactDocuments($companyName, $contacts, $contactDocumentFields);

        return true;
    }
    protected function getSafeFolderName($companyName)
    {
        // Use regular expression to remove unwanted characters (space, period, comma, etc.) at the end of the string
        $folderName = rtrim($companyName, " \t\n\r\0\x0B.,;!?");

        return $folderName;
    }
    protected function downloadAndSaveDocument($companyName, $documents)
    {
        $folderName = $this->getSafeFolderName($companyName);

        if (!Storage::disk('fsa')->exists($folderName)) {
            try {
                Storage::disk('fsa')->makeDirectory($folderName);
                Log::info("Created folder: {$folderName}");
            } catch (\Exception $e) {
                Log::error("Failed to create folder: {$folderName}. Error: " . $e->getMessage());
                return;
            }
        }

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:!23Qweasd');

        foreach ($documents as $document) {
            // Check if $document is an array and has 'showUrl' key
            if (!is_array($document) || !isset($document['showUrl'])) {
                Log::error("Invalid document data: " . var_export($document, true));
                continue;
            }

            $url = 'https://crm.cresco.ae/' . $document['showUrl'];
            curl_setopt($ch, CURLOPT_URL, $url);

            // Execute cURL request
            $response = curl_exec($ch);
            if ($response === false) {
                Log::error("Failed to download document: {$document['showUrl']}");
                continue;
            }

            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                $fileName = $this->extractFileName($headers, $url);
                $date = $this->extractLastModifiedDate($headers);
                $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_{$date}." . pathinfo($fileName, PATHINFO_EXTENSION);

                // Save the file directly to the NAS via FTP
                Storage::disk('fsa')->put("{$folderName}/{$newFileName}", $body);

                Log::info("Document saved successfully: {$folderName}/{$newFileName}");
            } else {
                Log::error("Failed to retrieve document. HTTP Code: $httpCode for {$document['showUrl']}");
            }
        }
        curl_close($ch);
    }
    protected function downloadAndSaveComapnyContactDocuments($companyName, $contacts, $contactDocumentFields)
    {
        $folderName = $this->getSafeFolderName($companyName);

        foreach ($contacts as $contact) {
            $folderName = "{$folderName}/{$contact['NAME']}";

            // Ensure folder creation, even if there are no documents
            if (!Storage::disk('fsa')->exists($folderName)) {
                try {
                    Storage::disk('fsa')->makeDirectory($folderName);
                    Log::info("Created folder: {$folderName}");
                } catch (\Exception $e) {
                    Log::error("Failed to create folder: {$folderName}. Error: " . $e->getMessage());
                    return;
                }
            }

            $filteredDocuments = [];
            foreach ($contactDocumentFields as $field) {
                if (!empty($contact[$field])) {
                    $filteredDocuments[] = $contact[$field];
                }
            }

            if (empty($filteredDocuments)) {
                Log::info("No documents found for contact: {$contact['NAME']}");
                continue;
            }

            foreach ($filteredDocuments as $document) {
                if (!is_array($document) || !isset($document['showUrl'])) {
                    Log::error("Invalid document data for {$contact['NAME']}: " . var_export($document, true));
                    continue;
                }

                $url = 'https://crm.cresco.ae/' . $document['showUrl'];

                // Initialize and execute cURL request
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_USERPWD => 'admin:!23Qweasd',
                ]);

                $response = curl_exec($ch);

                if ($response === false) {
                    Log::error("Failed to download document: {$document['showUrl']} for {$contact['NAME']}");
                    curl_close($ch);
                    continue;
                }

                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $headers = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                if ($httpCode == 200) {
                    $fileName = $this->extractFileName($headers, $url);
                    $date = $this->extractLastModifiedDate($headers);
                    $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_{$date}." . pathinfo($fileName, PATHINFO_EXTENSION);

                    Storage::disk('fsa')->put("{$folderName}/{$newFileName}", $body);
                    Log::info("Saved document: {$folderName}/{$newFileName}");
                } else {
                    Log::error("Failed to retrieve document. HTTP Code: $httpCode for {$document['showUrl']}");
                }
            }
        }
    }
    protected function saveCompanyRegister($companyName, $companyRegisterData)
    {
        try {
            $folderName = $this->getSafeFolderName($companyName);

            if (!Storage::disk('fsa')->exists($folderName)) {
                try {
                    Storage::disk('fsa')->makeDirectory($folderName);
                    Log::info("Created folder: {$folderName}");
                } catch (\Exception $e) {
                    Log::error("Failed to create folder: {$folderName}. Error: " . $e->getMessage());
                    return;
                }
            }

            // Convert seal image to base64
            $sealPath = public_path('img/HC-Seal_blue.png');
            if (file_exists($sealPath)) {
                $sealBase64 = base64_encode(file_get_contents($sealPath));
                $sealMimeType = mime_content_type($sealPath);
                $companyRegisterData['sealImage'] = "data:$sealMimeType;base64,$sealBase64";
            } else {
                Log::error("Seal image not found at $sealPath");
                $companyRegisterData['sealImage'] = null;
            }

            // Generate the PDF
            $pdf = WPDF::loadView('compliance.register', $companyRegisterData)
                ->setOption('enable-local-file-access', true)
                ->setOrientation('landscape');

            $pdfContent = $pdf->output();
            $remoteFilePath = "{$folderName}/{$folderName} - Register.pdf";

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);
            Log::info("Company register PDF saved for {$folderName}");
        } catch (\Exception $e){
            Log::error("Error while saving Company Register {$folderName}: " . $e->getMessage());
        }
    }
    private function extractFileName($headers, $url)
    {
        if (preg_match('/filename="([^"]+)"/', $headers, $matches)) {
            return $matches[1];
        } else {
            return basename(parse_url($url, PHP_URL_PATH));
        }
    }
    private function extractLastModifiedDate($headers)
    {
        if (preg_match('/last-modified: (.+)/i', $headers, $matches)) {
            return strtotime($matches[1]);
        } else {
            return time(); // Current Unix timestamp
        }
    }
    public function downloadFounderDocument($company, $foundersData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);
        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Founders.pdf";
            $pdf = WPDF::loadView('compliance.founders', $foundersData);
            $pdfContent = $pdf->setOrientation('landscape')->output();

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);

            Log::info("Company founders PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving fonder register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }
    public function downloadBeneficiaryDocument($company, $beneficiariesData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);

        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Beneficiary.pdf";

            $pdf = WPDF::loadView('compliance.beneficiaries', $beneficiariesData);
            $pdfContent =  $pdf->setOrientation('landscape')->output();
            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);

            Log::info("Company Beneficiaries PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving beneficiary register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }
    public function downloadProtectorDocument($company, $protectorsData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);

        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Protectors.pdf";

            $pdf = WPDF::loadView('compliance.protectors', $protectorsData);
            $pdfContent = $pdf->setOrientation('landscape')->output();

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);
            Log::info("Company Protectors PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving Protectors register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }
    public function downloadCouncilorDocument($company, $councilorsData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);

        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Councilors.pdf";

            $pdf = WPDF::loadView('compliance.councilors', $councilorsData);
            $pdfContent = $pdf->setOrientation('landscape')->output();

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);

            Log::info("Company Councilors PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving Councilor register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }
    public function downloadAuthorizedPersonDocument($company, $authorizedPersonsData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);

        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Authorized Persons.pdf";

            $pdf = WPDF::loadView('compliance.authorized_persons', $authorizedPersonsData);
            $pdfContent = $pdf->setOrientation('landscape')->output();

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);
            Log::info("Company Authorized Persons PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving Authorized Persons register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }
    public function downloadOfficersDocument($company, $officersData)
    {
        $folderName = $this->getSafeFolderName($company['company_name']);

        try {
            $remoteFilePath = "{$folderName}/{$folderName} - Register of Officers.pdf";

            $pdf = WPDF::loadView('compliance.officers', $officersData);
            $pdfContent = $pdf->setOrientation('landscape')->output();

            Storage::disk('fsa')->put($remoteFilePath, $pdfContent);
            Log::info("Company Officers PDF saved for {$folderName}");
            return true;
        } catch (\Exception $e){
            Log::error("Error while saving Officers register {$folderName} " . $e->getMessage(),[
                $e->getTrace()
            ]);
        }
    }

}
