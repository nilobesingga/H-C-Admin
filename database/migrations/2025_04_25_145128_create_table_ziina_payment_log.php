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
        Schema::create('ziina_payment_log', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->index();
            $table->string('account_id')->index();
            $table->string('operation_id')->index();
            $table->string('payment_link')->nullable();
            $table->string('status')->default('requires_payment_instrument')->comment('requires_payment_instrument, requires_user_action, pending, completed, failed , canceled');
            $table->string('currency');
            $table->decimal('amount', 10, 2);
            $table->json('latest_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_ziina_payment_log');
    }
};
