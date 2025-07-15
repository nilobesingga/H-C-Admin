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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contact_id');
            $table->string('name');
            $table->string('nationality')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('mobile')->nullable();
            $table->string('tin')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_place_of_issue')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_file')->nullable();
            $table->string('residence_visa_number')->nullable();
            $table->string('residence_visa_file_number')->nullable();
            $table->date('residence_visa_issue_date')->nullable();
            $table->date('residence_visa_expiry_date')->nullable();
            $table->string('residence_visa_file')->nullable();
            $table->string('emirates_id_number')->nullable();
            $table->date('emirates_id_issue_date')->nullable();
            $table->date('emirates_id_expiry_date')->nullable();
            $table->string('emirates_id_file')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
