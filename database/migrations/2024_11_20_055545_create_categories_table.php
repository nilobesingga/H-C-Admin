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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('categories')->insert([
            ['name' => 'Abode Options'], // 1
            ['name' => 'Bawbawon'], // 2
            ['name' => 'Bincres'], // 3
            ['name' => 'Cap Lion Point'],  // 4
            ['name' => 'CRESCO Accounting'],  // 5
            ['name' => 'CRESCO Compliance'],  // 6
            ['name' => 'CRESCO Holding'],  // 7
            ['name' => 'CRESCO Legal'],  // 8
            ['name' => 'CRESCO Power'],  // 9
            ['name' => 'CRESCO Tec'],  // 10
            ['name' => 'Hensley & Cook'],  // 11
            ['name' => 'Lionsrock'],  // 12
            ['name' => 'Sadiqa'],  // 13
            ['name' => 'SmartMoney'],  // 14
            ['name' => 'OrchidX'],  // 15
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
