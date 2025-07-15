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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id')->nullable();
            $table->string('bitrix_contact_id')->unique();
            $table->string('bitrix_user_id')->nullable();
            $table->string('bitrix_webhook_token')->nullable();
            $table->string('user_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('access_token')->unique();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_default_password')->default(false);
            $table->boolean('bitrix_active');
            $table->string('status')->default('offline');
            $table->dateTime('last_login')->nullable();
            $table->ipAddress('last_ip')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('type')->nullable()->comment('user,admin,client');
            $table->rememberToken();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        DB::table('users')->insert([
            ['parent_id' => 0, 'bitrix_user_id' => '118068', 'bitrix_webhook_token' => '6w3w42ec0aq4o1vk', 'email' => 'nilo.besingga@cresco.org', 'password' => bcrypt('123456789'), 'access_token' => md5('1nilo.besingga@cresco.org'), 'is_admin' => 1, 'bitrix_active' => 1]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
