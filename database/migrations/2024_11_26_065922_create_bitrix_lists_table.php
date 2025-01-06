<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitrix_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('bitrix_lists')->insert([
            ['name' => 'Purchase Invoice'],
            ['name' => 'Cash Requisition'],
            ['name' => 'Sales Invoices'],
            ['name' => 'Bank Transfers'],
            ['name' => 'Proforma Invoices'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitrix_lists');
    }
};
