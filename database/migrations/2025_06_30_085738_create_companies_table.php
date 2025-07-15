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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unique();
            $table->string('name');
            $table->string('license_number')->nullable();
            $table->string('incorporation_date')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('authority')->nullable();
            $table->string('organization_type')->nullable();
            $table->string('status')->default('active');
            $table->text('company_activity')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('registered_address')->nullable();
            $table->string('office_no')->nullable();
            $table->string('building_name')->nullable();
            $table->string('po_box')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->decimal('annual_turnover', 15, 2)->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
