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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('bitrix_user_id')->unique();
            $table->string('bitrix_name')->nullable();
            $table->string('bitrix_last_name')->nullable();
            $table->string('bitrix_second_name')->nullable();
            $table->string('bitrix_gender')->nullable();
            $table->string('bitrix_profession')->nullable();
            $table->string('bitrix_www')->nullable();
            $table->string('bitrix_birthday')->nullable();
            $table->string('bitrix_profile_photo')->nullable();
            $table->string('bitrix_icq')->nullable();
            $table->string('bitrix_phone')->nullable();
            $table->string('bitrix_fax')->nullable();
            $table->string('bitrix_mobile')->nullable();
            $table->string('bitrix_pager')->nullable();
            $table->string('bitrix_street')->nullable();
            $table->string('bitrix_city')->nullable();
            $table->string('bitrix_state')->nullable();
            $table->string('bitrix_zip')->nullable();
            $table->unsignedBigInteger('bitrix_country_id')->nullable();
            $table->string('bitrix_work_company')->nullable();
            $table->string('bitrix_work_position')->nullable();
            $table->string('bitrix_work_phone')->nullable();
            $table->string('bitrix_interests')->nullable();
            $table->string('bitrix_skills')->nullable();
            $table->string('bitrix_web_sites')->nullable();
            $table->string('bitrix_xing_link')->nullable();
            $table->string('bitrix_linkedin_link')->nullable();
            $table->string('bitrix_facebook_link')->nullable();
            $table->string('bitrix_twitter_link')->nullable();
            $table->string('bitrix_skype_link')->nullable();
            $table->string('bitrix_district')->nullable();
            $table->string('bitrix_phone_inner')->nullable();
            $table->string('bitrix_user_type')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
