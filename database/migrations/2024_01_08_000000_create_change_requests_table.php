<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('change_request', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');  // The model class being changed (e.g., Contact, Company)
            $table->unsignedBigInteger('model_id');  // The ID of the model being changed
            $table->string('field_name');  // The field that is being changed
            $table->text('current_value')->nullable();  // The current value
            $table->text('proposed_value')->nullable();  // The proposed new value
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();  // Reason for the change
            $table->unsignedBigInteger('requested_by');  // User who requested the change
            $table->unsignedBigInteger('reviewed_by')->nullable();  // User who reviewed the request
            $table->timestamp('reviewed_at')->nullable();  // When the request was reviewed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('change_request');
    }
};
