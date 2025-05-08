<?php

namespace App\Models\Qashio;

use Illuminate\Database\Eloquent\Model;

class QashioTransaction extends Model
{
    protected $table = 'qashio_transactions';
    protected $fillable = [
        'id', 'qashioId', 'stringId', 'parentId', 'rrn', 'transactionAmount', 'transactionFeeAmount', 'transactionCurrency',
        'transactionTime', 'transactionStatus', 'transactionDescription', 'transactionType',
        'transactionCategory', 'clearingAmount', 'clearingFee', 'clearingStatus', 'vatAmount',
        'clearedAt', 'billingAmount', 'billingCurrency', 'memo', 'status_code', 'messageType',
        'settlementStatus', 'vendorTrn', 'purchaseOrderNumber', 'visible',
        'excludeFromSync', 'syncErrors', 'erpSyncStatus', 'erpSyncType', 'approvalStatus',
        'segments', 'merchantName', 'erpSupplierName', 'erpSupplierRemoteId', 'expenseCategoryName',
        'erpTaxRateName', 'erpTaxRateRemoteId', 'erpChatOfAccountName', 'erpChatOfAccountRemoteId',
        'poolAccountName', 'cardName', 'cardLastFour', 'cardHolderName', 'cardHolderEmail',
        'receipts', 'lineItems', 'createdAt', 'updatedAt', 'last_sync', 'bitrix_cash_request_id',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'excludeFromSync' => 'boolean',
        'segments' => 'array',
        'receipts' => 'array',
        'lineItems' => 'array',
    ];
}
