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
        Schema::create('contact_relationship', function (Blueprint $table) {
            $table->id();
            $table->string('tec_custom_field_id')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('owner_type')->nullable();
            $table->string('owner')->nullable();
            $table->string('name')->nullable();
            $table->string('owner_reverse')->nullable();
            $table->string('owner_id_reverse')->nullable();
            $table->string('owner_type_reverse')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('shares')->nullable();
            $table->string('nominee_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_relationship');
    }
};
