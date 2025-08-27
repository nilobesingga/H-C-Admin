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
        Schema::create('request_conversation_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_conversation_id')->constrained('request_conversation')->onDelete('cascade');
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_conversation_file');
    }
};
