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
        Schema::create('qashio_bitrix_merchant_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('qashio_name')->unique();
            $table->string('bitrix_company_id')->nullable();
            $table->string('bitrix_company_name')->nullable();
            $table->string('bitrix_contact_id')->nullable();
            $table->string('bitrix_contact_name')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qashio_bitrix_merchant_mapping');
    }
};
