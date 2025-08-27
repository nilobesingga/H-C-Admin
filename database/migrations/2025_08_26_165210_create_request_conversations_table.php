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
        Schema::create('request_conversation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('request')->onDelete('cascade');
            $table->text('message');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_conversation');
    }
};
