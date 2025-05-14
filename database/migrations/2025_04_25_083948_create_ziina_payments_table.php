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
        Schema::create('ziina_payment', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->string('account_id');
            $table->string('operation_id');
            $table->string('payment_link')->nullable();
            $table->string('status')->default('requires_payment_instrument')->comment('requires_payment_instrument, requires_user_action, pending, completed, failed , canceled');
            $table->string('currency');
            $table->decimal('amount', 12, 2);
            $table->decimal('service_charge', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('message')->nullable();
            $table->string('success_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('failure_url')->nullable();
            $table->string('expiry')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->dateTime('payment_completed_at')->nullable();
            $table->json('latest_error')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('filename')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ziina_payment');
    }
};
