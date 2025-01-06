<?php

namespace App\Models\Bitrix;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $table = 'user_profile';
    protected $fillable = [
        'user_id', 'bitrix_user_id', 'bitrix_name', 'bitrix_last_name', 'bitrix_second_name', 'bitrix_gender', 'bitrix_profession', 'bitrix_www',
        'bitrix_birthday', 'bitrix_profile_photo', 'bitrix_icq', 'bitrix_phone', 'bitrix_fax', 'bitrix_mobile', 'bitrix_pager',
        'bitrix_street', 'bitrix_city', 'bitrix_state', 'bitrix_zip', 'bitrix_country_id', 'bitrix_work_company', 'bitrix_work_position',
        'bitrix_work_phone', 'bitrix_interests', 'bitrix_skills', 'bitrix_web_sites', 'bitrix_xing_link', 'bitrix_linkedin_link',
        'bitrix_facebook_link', 'bitrix_twitter_link', 'bitrix_skype_link', 'bitrix_district', 'bitrix_phone_inner', 'bitrix_user_type'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
