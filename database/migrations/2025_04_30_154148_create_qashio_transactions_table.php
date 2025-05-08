<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qashio_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('qashioId')->unique();
            $table->string('stringId');
            $table->string('parentId')->nullable();
            $table->string('rrn')->nullable();
            // Transaction-related fields
            $table->decimal('transactionAmount', 10, 2)->nullable();
            $table->decimal('transactionFeeAmount', 10, 2)->nullable();
            $table->string('transactionCurrency')->nullable();
            $table->string('transactionTime')->nullable();
            $table->string('transactionStatus')->nullable();
            $table->string('transactionDescription')->nullable();
            $table->string('transactionType')->nullable();
            $table->string('transactionCategory')->nullable();
            // Clearing-related fields
            $table->decimal('clearingAmount', 10, 2)->nullable();
            $table->decimal('clearingFee', 10, 2)->nullable();
            $table->string('clearingStatus')->nullable();
            $table->decimal('vatAmount', 10, 2)->nullable();
            $table->string('clearedAt')->nullable();
            // Billing-related fields
            $table->decimal('billingAmount', 10, 2)->nullable();
            $table->string('billingCurrency')->nullable();
            // Status and other fields
            $table->text('memo')->nullable();
            $table->string('status_code')->nullable();
            $table->string('messageType')->nullable();
            $table->string('settlementStatus')->nullable();
            $table->string('vendorTrn')->nullable();
            $table->string('purchaseOrderNumber')->nullable();
            $table->boolean('visible')->nullable();
            $table->boolean('excludeFromSync')->nullable()->default(false);
            $table->json('syncErrors')->nullable();
            $table->string('erpSyncStatus')->nullable();
            $table->string('erpSyncType')->nullable();
            $table->string('approvalStatus')->nullable();
            $table->json('segments')->nullable();
            $table->string('merchantName')->nullable();
            $table->string('erpSupplierName')->nullable();
            $table->string('erpSupplierRemoteId')->nullable();
            $table->string('expenseCategoryName')->nullable();
            $table->string('erpTaxRateName')->nullable();
            $table->string('erpTaxRateRemoteId')->nullable();
            $table->string('erpChatOfAccountName')->nullable();
            $table->string('erpChatOfAccountRemoteId')->nullable();
            $table->string('poolAccountName')->nullable();
            $table->string('cardName')->nullable();
            $table->string('cardLastFour')->nullable();
            $table->string('cardHolderName')->nullable();
            $table->string('cardHolderEmail')->nullable();
            $table->json('receipts')->nullable();
            $table->json('lineItems')->nullable();
            $table->string('createdAt')->nullable();
            $table->string('updatedAt')->nullable();
            $table->dateTime('last_sync');
            $table->string('bitrix_cash_request_id')->nullable();
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qashio_transactions');
    }
};
