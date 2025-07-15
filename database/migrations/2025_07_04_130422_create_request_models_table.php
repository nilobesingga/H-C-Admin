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
        Schema::create('request', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->bigInteger('company_id');
            $table->string('category');
            $table->text('description')->nullable();
            $table->enum('type', ['document_request', 'change_request'])->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('status')->default('pending')->comment('pending, declined, approved, cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request');
    }
};
