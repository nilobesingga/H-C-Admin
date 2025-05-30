<?php

namespace App\Services;

use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\Qashio\QashioBitrixMerchantMapping;
use App\Models\Qashio\QashioTransaction;
use App\Repositories\BitrixApiRepository;
use App\Repositories\QashioApiRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use PhpParser\Node\Expr\Cast\Object_;

Class QashioService
{
    protected $qashioRepo;
    protected $bitrixApiRepo;

    public function __construct(QashioApiRepository $qashioRepo, BitrixApiRepository $bitrixApiRepo)
    {
        $this->qashioRepo = $qashioRepo;
        $this->bitrixApiRepo = $bitrixApiRepo;
    }
    public function syncQashioTransactions()
    {
        try {
            $lastTransactionDateTime = QashioTransaction::where([
                'transactionCategory' => 'purchase',
                'clearingStatus' => 'pending',
            ])->min('transactionTime');

            // Prepare API parameters
            $params = ['limit' => 50000];

            if ($lastTransactionDateTime) {
                // Incremental sync: fetch only new transactions after last sync
                $params['transactionTimeFrom'] = Carbon::parse($lastTransactionDateTime)->toIso8601String();
            } else {
                Log::info('Performing initial sync of all Qashio transactions.');
                $params['transactionTimeFrom'] = "2025-05-01T00:00:00.000Z";
            }
            $params['transactionTimeTo'] = Carbon::now()->toIso8601String();

            // Fetch transactions
            $response = $this->qashioRepo->getTransactions($params);
            $transactions = $response['data'] ?? [];

            Log::info('Fetched ' . count($transactions) . ' transactions from ' . $params['transactionTimeFrom'] . ' transactions to ' . $params['transactionTimeTo']);

            if (empty($transactions)) {
                Log::warning('No transactions found to sync.');
                return [
                    'success' => false,
                    'message' => 'No new transactions to sync.',
                ];
            }

            $result = $this->processTransactions($transactions);

            return [
                'success' => true,
                'message' => 'Transactions synced and processed successfully.',
                'count' => count($transactions),
                'updated_bitrix' => $result['updated_bitrix'] ?? 0,
                'failed_bitrix' => $result['failed_bitrix'] ?? 0,
            ];

        } catch (\Exception $e) {
            Log::error('Error syncing Qashio transactions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'Failed to sync transactions.',
                'error' => $e->getMessage(),
            ];
        }
    }
    public function processTransactions($transactions)
    {
        $upsertData = [];
        $existingTransactions = QashioTransaction::where('clearingStatus', 'pending')
            ->whereIn('qashioId', array_column($transactions, 'qashioId'))
            ->get()
            ->keyBy('qashioId');

        $updatedBitrixCount = 0;
        $failedBitrixCount = 0;

        foreach ($transactions as $transaction) {
            $qashioId = $transaction['qashioId'];
            $transactionData = $this->prepareTransactionData('isSync', $transaction);
            if (isset($existingTransactions[$qashioId])) {
                $existingTransaction = $existingTransactions[$qashioId];
                if ($transaction['transactionCategory'] === 'purchase' && $existingTransaction->clearingStatus === 'pending' && in_array($transaction['clearingStatus'], ['cleared', 'updated', 'reversed']) && !empty($existingTransaction->bitrix_cash_request_id)) {
                    $bitrixUpdateResult = $this->updateBitrixCashRequest($existingTransaction, $transaction);
                    if ($bitrixUpdateResult['success']) {
                        $updatedBitrixCount++;
                    } else {
                        $failedBitrixCount++;
                        Log::warning("Failed to update Bitrix for transaction: {$qashioId}", [
                            'error' => $bitrixUpdateResult['error'] ?? 'Unknown error',
                        ]);
                    }
                }
            }
            $upsertData[] = $transactionData;
        }

        if (!empty($upsertData)) {
            try {
                $uniqueBy = ['qashioId'];
                $updateColumns = array_keys($upsertData[0]);
                // Exclude fields that shouldn’t be updated
                $excludeColumns = ['qashioId', 'bitrix_qashio_credit_card_category_id', 'bitrix_qashio_credit_card_sage_company_id', 'created_at'];
                $updateColumns = array_diff($updateColumns, $excludeColumns);

                QashioTransaction::upsert($upsertData, $uniqueBy, $updateColumns);
                $upsertedTransactionsCount = count($upsertData);
                Log::info('Upserted ' . $upsertedTransactionsCount . ' transactions.');
            } catch (\Exception $e) {
                Log::error('Failed to upsert Qashio transactions', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return [
                    'updated_bitrix' => $updatedBitrixCount,
                    'failed_bitrix' => $failedBitrixCount,
                    'upserted_transactions' => 0,
                    'error' => 'Failed to upsert transactions: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'updated_bitrix' => $updatedBitrixCount,
            'failed_bitrix' => $failedBitrixCount,
        ];
    }
    protected function prepareTransactionData($mode, $transaction)
    {
        $bitrixQashioCreditCard['category_id'] = null;
        $bitrixQashioCreditCard['sage_company_id'] = null;

        if($mode === 'isSync'){
            $bitrixQashioCreditCard = $this->bitrixApiRepo->getQashioCreditCardByCardLastFourDigit($transaction['cardLastFour']);
        }

        return [
            'qashioId' => $transaction['qashioId'],
            'stringId' => $transaction['id'],
            'parentId' => $transaction['parentId'] ?? null,
            'rrn' => $transaction['rrn'] ?? null,
            'transactionAmount' => $transaction['transactionAmount'] ?? null,
            'transactionFeeAmount' => $transaction['transactionFeeAmount'] ?? null,
            'transactionCurrency' => $transaction['transactionCurrency'] ?? null,
            'transactionTime' => $transaction['transactionTime'] ?? null,
            'transactionStatus' => $transaction['transactionStatus'] ?? null,
            'transactionDescription' => $transaction['transactionDescription'] ?? null,
            'transactionType' => $transaction['transactionType'] ?? null,
            'transactionCategory' => $transaction['transactionCategory'] ?? null,
            'clearingAmount' => $transaction['clearingAmount'] ?? null,
            'clearingFee' => $transaction['clearingFee'] ?? null,
            'clearingStatus' => $transaction['clearingStatus'] ?? null,
            'vatAmount' => $transaction['vatAmount'] ?? null,
            'clearedAt' => $transaction['clearedAt'] ?? null,
            'billingAmount' => $transaction['billingAmount'] ?? null,
            'billingCurrency' => $transaction['billingCurrency'] ?? null,
            'memo' => $transaction['memo'] ?? null,
            'status_code' => $transaction['status_code'] ?? null,
            'messageType' => $transaction['messageType'] ?? null,
            'settlementStatus' => $transaction['settlementStatus'] ?? null,
            'vendorTrn' => $transaction['vendorTrn'] ?? null,
            'purchaseOrderNumber' => $transaction['purchaseOrderNumber'] ?? null,
            'visible' => $transaction['visible'] ?? true,
            'excludeFromSync' => $transaction['excludeFromSync'] ?? false,
            'syncErrors' => isset($transaction['syncErrors']) ? json_encode($transaction['syncErrors']) : null,
            'erpSyncStatus' => $transaction['erpSyncStatus'] ?? null,
            'erpSyncType' => $transaction['erpSyncType'] ?? null,
            'approvalStatus' => $transaction['approvalStatus'] ?? null,
            'segments' => isset($transaction['segments']) ? json_encode($transaction['segments']) : null,
            'merchantName' => $transaction['merchantName'] ?? null,
            'erpSupplierName' => $transaction['erpSupplierName'] ?? null,
            'erpSupplierRemoteId' => $transaction['erpSupplierRemoteId'] ?? null,
            'expenseCategoryName' => $transaction['expenseCategoryName'] ?? null,
            'erpTaxRateName' => $transaction['erpTaxRateName'] ?? null,
            'erpTaxRateRemoteId' => $transaction['erpTaxRateRemoteId'] ?? null,
            'erpChatOfAccountName' => $transaction['erpChatOfAccountName'] ?? null,
            'erpChatOfAccountRemoteId' => $transaction['erpChatOfAccountRemoteId'] ?? null,
            'poolAccountName' => $transaction['poolAccountName'] ?? null,
            'cardName' => $transaction['cardName'] ?? null,
            'cardLastFour' => $transaction['cardLastFour'] ?? 0,
            'cardHolderName' => $transaction['cardHolderName'] ?? null,
            'cardHolderEmail' => $transaction['cardHolderEmail'] ?? null,
            'receipts' => isset($transaction['receipts']) ? json_encode($transaction['receipts']) : null,
            'lineItems' => isset($transaction['lineItems']) ? json_encode($transaction['lineItems']) : null,
            'createdAt' => $transaction['createdAt'] ?? null,
            'updatedAt' => $transaction['updatedAt'] ?? null,
            'last_sync' => now(),
            'bitrix_qashio_credit_card_category_id' => $bitrixQashioCreditCard['category_id'] ?? null,
            'bitrix_qashio_credit_card_sage_company_id' => $bitrixQashioCreditCard['sage_company_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    public function createBitrixCashRequest($request)
    {
        try {
            // Parse form data
            $data = json_decode($request->input('data'), true);
            $files = $request->file('files', []);

            // Initialize arrays for file IDs
            $bitrixFileIds = [];

            // Bitrix folder info
            // Bitrix Table b_disk_object, ID = 682645, Name = Cash Requisition,

            // Handle Qashio receipt URLs
            if (!empty($data['receipts'])) {
                $qashioUrls = array_filter(array_column($data['receipts'], 'url'));
                if (!empty($qashioUrls)) {
                    $uploadResults = $this->bitrixApiRepo->uploadDocumentToBitrixDriveByURLsArray(685972, $qashioUrls);
                    foreach ($uploadResults as $result) {
                        if ($result['status'] === 'success' && isset($result['id'])) {
                            $bitrixFileIds[] = $result['id'];
                        } else {
                            Log::warning("Failed to upload Qashio receipt: {$result['file']}", ['error' => $result['error'] ?? 'Unknown error']);
                        }
                    }
                }
            }

            // Handle user-uploaded files
            if (!empty($files)) {
                $fileNames = array_column($data['receipts'], 'name');
                $uploadResults = $this->bitrixApiRepo->uploadDocumentToBitrixDriveByUserUploads(685972, $files, $fileNames);
                foreach ($uploadResults as $result) {
                    if ($result['status'] === 'success' && isset($result['id'])) {
                        $bitrixFileIds[] = $result['id'];
                    } else {
                        Log::warning("Failed to upload user file: {$result['file']}", ['error' => $result['error'] ?? 'Unknown error']);
                    }
                }
            }

            // Prepare Bitrix data
            $qashioTransaction = $data['qashio_object'];
            $bitrixCashRequestData = $this->prepareBitrixCashRequisitionData('add', $data, $bitrixFileIds, $qashioTransaction);
            $bitrixId = $this->bitrixApiRepo->createCashRequisition('qashio', $bitrixCashRequestData);

            if ($bitrixId) {
                QashioBitrixMerchantMapping::updateOrCreate(
                    ['qashio_name' => $qashioTransaction['merchantName']],
                    [
                        'bitrix_company_id' => $data['supplier']['ID'],
                        'bitrix_company_name' => $data['supplier']['TITLE'],
                        'is_active' => 1,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );

                QashioTransaction::where('qashioId', $qashioTransaction['qashioId'])->update([
                    'bitrix_cash_request_id' => $bitrixId,
                ]);

                Log::info("Created Bitrix cash request for transaction: {$qashioTransaction['qashioId']}", ['bitrix_id' => $bitrixId]);
                return [
                    'success' => true,
                    'bitrix_id' => $bitrixId,
                ];
            }

        } catch (\Exception $e) {
            Log::error("Failed to create Bitrix cash request for transaction: {$qashioTransaction['qashioId']}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    public function prepareBitrixCashRequisitionData($action, $data, $fileIds = [], $qashioTransaction = null)
    {
        $formattedFileIds = [];
        foreach (array_filter($fileIds, 'is_numeric') as $index => $fileId) {
            $formattedFileIds['n' . $index] = 'n' . (int) $fileId;
        }

        if ($action === 'add') {
            return [
                'NAME' => $data['name'],
                // Company / Category
                'PROPERTY_938' => $data['category_id'],
                // Sage Company
                'PROPERTY_951' => $data['sage_company_id'],
                // Vatable 2219 yes and 2218 No
                'PROPERTY_1236' => $data['vatable_id'],
                // Amount
                'PROPERTY_939' => $data['amount'] . '|' . $data['currency'],
                // Amount Given
                'PROPERTY_944' => $data['amount_given'],
                // Awaiting for Exchange Rate
                'PROPERTY_1249' => $data['awaiting_for_exchange_rate_id'],
                // Cash Release Location
                'PROPERTY_954' => $data['cash_release_location_id'],
                // Invoice Number
                'PROPERTY_1241' => $data['invoice_number'],
                // Payment Date
                'PROPERTY_940' => $data['payment_date'],
                // Payment Mode
                'PROPERTY_1088' => $data['payment_mode_id'],
                // Budget only
                'PROPERTY_1160' => $data['budget_only_id'],
                // Charge extra to client:
                'PROPERTY_1215' => $data['charge_extra_to_client_id'],
                // Charge To Running Account:
                'PROPERTY_1243' => $data['charge_to_running_account_id'],
                // Project
                'PROPERTY_942' => $data['project']['TYPE'] == 1 ? 'L_' . $data['project']['ID'] : 'D_' .$data['project']['ID'],
                // Supplier
                'PROPERTY_1234' => 'CO_' . $data['supplier']['ID'],
                // Pay To Running Account
                'PROPERTY_1251' => $data['pay_to_running_account_id'],
                // Remarks
                'DETAIL_TEXT' => $data['remarks'],
                // Receipts
                'PROPERTY_948' => $formattedFileIds,
                // Status 1652 = Approved and 1655 Cash Released
                'PROPERTY_943' => $qashioTransaction['clearingStatus'] === 'cleared' || $qashioTransaction['clearingStatus'] === 'updated' ? '1655' : '1652',
                // Funds Available Date
                'PROPERTY_946' => $data['payment_date'],
                // Qashio Id
                'PROPERTY_1284' => $qashioTransaction['qashioId'],
                // Modified By
                'MODIFIED_BY' => Auth::user()->bitrix_user_id,
                // Released By
                'PROPERTY_1071' => $data['release_by'] ?? null,
                // Released Date
                'PROPERTY_1073' => $data['release_date'] ?? null,
                // CREATED_BY
                'CREATED_BY' => Auth::user()->bitrix_user_id,
                // Created Date
                'DATE_CREATE' => Carbon::now()->format('d.m.Y H:i:s'),
            ];
        }
    }
    public function updateBitrixCashRequest($existingTransaction, $qashioTransaction)
    {
        try {
            if (empty($existingTransaction->bitrix_cash_request_id)) {
                Log::warning("No Bitrix cash request ID found for transaction: {$existingTransaction->qashioId}");
                return [
                    'success' => false,
                    'error' => 'No Bitrix cash request ID associated with this transaction.',
                ];
            }

            // Fetch current Bitrix cash requisition
            $bitrixFields = $this->bitrixApiRepo->getCashRequisitionById($existingTransaction->bitrix_cash_request_id);

            if(!empty($bitrixFields)){
                if(in_array($qashioTransaction['clearingStatus'], ['cleared', 'updated'])){
                    $clearingAmount = floatval($qashioTransaction['clearingAmount']);
                    $clearingFee = floatval($qashioTransaction['clearingFee'] ?? 0);
                    $totalAmount = $clearingAmount + $clearingFee;

                    // Amount
                    $bitrixFields['PROPERTY_939'] = $totalAmount . '|' . $qashioTransaction['billingCurrency'];
                    // Amount Given
                    $bitrixFields['PROPERTY_944'] = $totalAmount;
                    // Awaiting for Exchange Rate  2268 = Yes , 2269 = No
                    $bitrixFields['PROPERTY_1249'] = '2269';
                    // Status 1652 = Approved and 1655 Cash Released
                    $bitrixFields['PROPERTY_943'] = '1655';
                    // Modified By
                    $bitrixFields['MODIFIED_BY'] = 'Admin';
                    // Name
                    $bitrixFields['NAME'] = 'Cash Request - ' . $bitrixFields['PROPERTY_939'];
                    // Released By
                    $bitrixFields['PROPERTY_1071'] = 'Admin';
                    // Released Date
                    $bitrixFields['PROPERTY_1073'] =  Carbon::parse($qashioTransaction['clearedAt'])->format('d.m.Y');
                    // Update Reason
                    $bitrixFields['PROPERTY_1276'] = 'Updated by CRON job';
                    // Append to DETAIL_TEXT if currency is converted
                    if ($qashioTransaction['transactionCurrency'] !== 'AED') {
                        $convertedLine = "Converted " . number_format($qashioTransaction['transactionAmount'], 2) . " " .
                            $qashioTransaction['transactionCurrency'] . " to " .
                            number_format($totalAmount, 2) . " " .
                            $qashioTransaction['billingCurrency'];

                        // Prevent duplicate entries
                        if (!str_contains($bitrixFields['DETAIL_TEXT'], $convertedLine)) {
                            $bitrixFields['DETAIL_TEXT'] = trim($bitrixFields['DETAIL_TEXT']) . "\n" . $convertedLine;
                        }
                    }
                }
                if ($qashioTransaction['clearingStatus'] === 'reversed'){
                    // Status 1659 = Cancelled
                    $bitrixFields['PROPERTY_943'] = '1659';
                    // Modified By
                    $bitrixFields['MODIFIED_BY'] = 'Admin';
                    // Update Reason
                    $bitrixFields['PROPERTY_1276'] = 'Reversed by CRON job';
                }
            }

            // Update Bitrix cash requisition
            $updateResult = $this->bitrixApiRepo->updateCashRequisition($existingTransaction->bitrix_cash_request_id, $bitrixFields);
            if($updateResult){
                Log::info("Updated Bitrix cash request for transaction: {$qashioTransaction['qashioId']}");
                return [
                    'success' => true,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to update Bitrix cash request for transaction: {$qashioTransaction['qashioId']}", [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
