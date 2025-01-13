<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('modules')->insert([
            ['parent_id' => 0, 'name' => 'Reports', 'slug' => Str::slug('Reports', '-'), 'route' => '/reports'],
            ['parent_id' => 1, 'name' => 'Purchase Invoices', 'slug' => Str::slug('Purchase Invoices', '-'), 'route' => '/reports/purchase-invoices'],
            ['parent_id' => 1, 'name' => 'Cash Requests', 'slug' => Str::slug('Cash Requests', '-'), 'route' => '/reports/cash-requests'],
            ['parent_id' => 1, 'name' => 'Bank Transfers', 'slug' => Str::slug('Bank Transfers', '-'), 'route' => '/reports/bank-transfers'],
            ['parent_id' => 1, 'name' => 'Sales Invoices', 'slug' => Str::slug('Sales Invoices', '-'), 'route' => '/reports/sales-invoices'],
            ['parent_id' => 1, 'name' => 'Proforma Invoices', 'slug' => Str::slug('Proforma Invoices', '-'), 'route' => '/reports/proforma-invoices'],
            ['parent_id' => 1, 'name' => 'Bank Summary', 'slug' => Str::slug('Bank Summary', '-'), 'route' => '/reports/bank-summary'],
            ['parent_id' => 1, 'name' => 'Expense Planner', 'slug' => Str::slug('Expense Planner', '-'), 'route' => '/reports/expense-planner'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
