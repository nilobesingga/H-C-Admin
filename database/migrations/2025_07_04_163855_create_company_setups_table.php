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
        Schema::create('company_setup', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('timeframe');
            $table->string('language');
            $table->string('contact_method')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_setup');
    }
};
