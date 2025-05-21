<?php

namespace App\Services;

use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\Qashio\QashioTransaction;
use App\Repositories\BitrixApiRepository;
use App\Repositories\QashioApiRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            // Get the latest transaction time from the local database
            $lastSync = QashioTransaction::max('transactionTime');

            // Prepare API parameters
            $params = ['limit' => 50000];

            if ($lastSync) {
                // Incremental sync: fetch only new transactions after last sync
                $params['transactionTimeFrom'] = Carbon::parse($lastSync)->addSecond()->toIso8601String();
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

//            $this->processTransactions($transactions);
            // Only Sync to database no bitrix cash requisitions creating.
            $upsertData = [];

            foreach ($transactions as $transaction) {
                $qashioId = $transaction['qashioId'];
                $upsertData[] = $this->prepareTransactionData($transaction);
            }

            if (!empty($upsertData)) {
                $uniqueBy = ['qashioId'];
                $updateColumns = array_keys($upsertData[0]);
                unset($updateColumns[array_search('qashioId', $updateColumns)]);
                unset($updateColumns[array_search('created_at', $updateColumns)]);

                QashioTransaction::upsert($upsertData, $uniqueBy, $updateColumns);
                Log::info('Upserted ' . count($upsertData) . ' transactions.');
            }

            return [
                'success' => true,
                'message' => 'Transactions synced and processed successfully.',
                'count' => count($transactions),
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
        $existingTransactions = QashioTransaction::whereIn('qashioId', array_column($transactions, 'qashioId'))
            ->get()
            ->keyBy('qashioId');

        foreach ($transactions as $transaction) {
            $qashioId = $transaction['qashioId'];
            $transactionData = $this->prepareTransactionData($transaction);

            if (isset($existingTransactions[$qashioId])) {
                $existingTransaction = $existingTransactions[$qashioId];
                if ($transaction['transactionCategory'] === 'purchase' && $existingTransaction->clearingStatus === 'pending' &&  in_array($transaction['clearingStatus'], ['cleared', 'updated'])) {
                    dd('processTransactions before creating bitrix cash request', $transaction);
                    $this->updateBitrixCashRequest($existingTransaction);
                }
                $upsertData[] = $transactionData;
            } else {
                $upsertData[] = $transactionData;
                if ($transaction['transactionCategory'] === 'purchase' && in_array($transaction['clearingStatus'], ['pending', 'cleared'])) {
                    $bitrixId = $this->createBitrixCashRequest('sync', $transaction);
                    if ($bitrixId) {
                        $transactionData['bitrix_cash_request_id'] = $bitrixId;
                    }
                }
            }
        }

        if (!empty($upsertData)) {
            $uniqueBy = ['qashioId'];
            $updateColumns = array_keys($upsertData[0]);
            unset($updateColumns[array_search('qashioId', $updateColumns)]);
            unset($updateColumns[array_search('created_at', $updateColumns)]);

            QashioTransaction::upsert($upsertData, $uniqueBy, $updateColumns);
            Log::info('Upserted ' . count($upsertData) . ' transactions.');
        }
    }
    protected function prepareTransactionData($transaction)
    {
        $bitrixQashioCreditCard = $this->bitrixApiRepo->getQashioCreditCardByCardLastFourDigit($transaction['cardLastFour']);
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
    public function createBitrixCashRequest($mode, $transaction)
    {
        try {
            $bitrixCashRequestData = $this->makingBitrixCashRequestData('add', $transaction);
            $bitrixId = $this->bitrixApiRepo->createCashRequisition($bitrixCashRequestData);

            if ($bitrixId) {
                Log::info("Created Bitrix cash request for transaction: {$transaction['qashioId']}", ['bitrix_id' => $bitrixId]);
            }

            return $bitrixId;
        } catch (\Exception $e) {
            Log::error("Failed to create Bitrix cash request for transaction: {$transaction['qashioId']}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    public function updateBitrixCashRequest($transaction)
    {
        try {
            dd('from updateBitrixCashRequest', $transaction);
            $this->bitrixService->updateCashRequest($transaction->bitrix_cash_request_id, [
                'status' => 'cleared',
            ]);

            Log::info("Updated Bitrix cash request for transaction: {$transaction->qashioId}");
        } catch (\Exception $e) {
            Log::error("Failed to update Bitrix cash request for transaction: {$transaction->qashioId}", [
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function makingBitrixCashRequestData($type, $qashioTransaction)
    {
        if ($type === 'add'){
            $amount = $qashioTransaction['clearingStatus'] === 'pending' ? $qashioTransaction['transactionAmount'] : $qashioTransaction['clearingAmount'];
            $currency = $qashioTransaction['clearingStatus'] === 'pending' ? $qashioTransaction['transactionCurrency'] : $qashioTransaction['billingCurrency'];

            $documentFieldData = null;
            if (!empty($qashioTransaction['receipts'])) {
                $bitrixUploadedReceiptFiles = $this->bitrixApiRepo->uploadDocumentToBitrixDriveByURLsArray(490, $qashioTransaction['receipts']);

                // Build PROPERTY_948
                $successfulIds = collect($bitrixUploadedReceiptFiles)
                    ->where('status', 'success')
                    ->pluck('id')
                    ->values();

                if ($successfulIds->isNotEmpty()) {
                    $documentFieldData = [];
                    foreach ($successfulIds as $index => $id) {
                        $documentFieldData["n$index"] = "n$id";
                    }
                }
            }

            $projectId = null;
            if (!empty($qashioTransaction['memo'])) {
                // Extract prefix
                preg_match('/^[A-Z]_\d{2}-\d{3}/', $qashioTransaction['memo'], $matches);
                if (!empty($matches)) {
                    $prefix = $matches[0];
                    $project = $this->bitrixApiRepo->getProjectByDealPrefixNumber($prefix);
                    if (!empty($project[0])) {
                        $projectId = $project[0]['ID'];
                    }
                }
            }

            return (Object)[
                'NAME' => "Cash Request - {$amount}|{$currency}",
                // Company / Category
                'PROPERTY_938' => $qashioTransaction['bitrix_qashio_credit_card_category_id'],
                // Sage Company
                'PROPERTY_951' => $qashioTransaction['bitrix_qashio_credit_card_sage_company_id'],
                // Vatable 2219 yes and 2218 No
                'PROPERTY_1236' => $qashioTransaction['erpTaxRateName'] === "Standard Rate" ? '2219' : '2218',
                // Amount
                'PROPERTY_939' => $amount . '|' . $currency,
                // Awaiting for Exchange Rate
                'PROPERTY_1249' => $currency !== 'AED' ? "2268" : "2269",
                // Cash Release Location
                'PROPERTY_954' => '1681', // Dubai
                // Invoice Number
                'PROPERTY_1241' => $qashioTransaction['purchaseOrderNumber'],
                // Payment Date
                'PROPERTY_940' => Carbon::parse($qashioTransaction['transactionTime'])->format('Y-m-d'),
                // Payment Mode
                'PROPERTY_1088' => '2352', // Qashio
                // Project
                'PROPERTY_942' => 'D_' .  $projectId,
                // Remarks
                'DETAIL_TEXT' => "Memo: {$qashioTransaction['memo']}\nMerchant: {$qashioTransaction['merchantName']}\nCategory Name: {$qashioTransaction['expenseCategoryName']}",
                // Funds Available Date
                'PROPERTY_946' => Carbon::parse($qashioTransaction['transactionTime'])->format('Y-m-d'),
                // Status 1652 = Approved and 1655 Cash Released
                'PROPERTY_943' => $qashioTransaction['clearingStatus'] === 'pending' ? '1652' : '1655',
                // Receipts
                'PROPERTY_948' => $documentFieldData,
                // Qashio Id
                'PROPERTY_1284' => $qashioTransaction['qashioId'],
                // Modified By
                'MODIFIED_BY' => Auth::user()->bitrix_user_id,
                // Created Date
                'DATE_CREATE' => Carbon::now()->format('d.m.Y H:i:s'),
            ];
        }

    }

}
