<?php

namespace App\Services\Bitrix;

use App\Models\Bitrix\UserProfile;
use App\Models\User;
use App\Repositories\BitrixApiRepository;
use Illuminate\Support\Facades\DB;

class BitrixService
{
    protected $bitrixRepo;
    public function __construct(BitrixApiRepository $bitrixRepo)
    {
        $this->bitrixRepo = $bitrixRepo;
    }
    public function syncBitrixUsers()
    {
        ini_set('max_execution_time', 500);
        $bitrixUsers = $this->bitrixRepo->getAllBitrixUsers();

        foreach ($bitrixUsers as $bitrixUser) {
            DB::transaction(function () use ($bitrixUser) {
                // Check if user exists
                $userExists = User::where('bitrix_user_id', $bitrixUser['ID'])->exists();
                // Prepare user data with conditional fields
                $userData = [
                    'bitrix_active' => $bitrixUser['ACTIVE'],
                    'updated_by' => 0,
                ];
                // Add fields only for new users
                if (!$userExists) {
                    $userData = array_merge($userData, [
                        'email' => $bitrixUser['EMAIL'],
                        'password' => bcrypt('123456789'),
                        'access_token' => md5($bitrixUser['ID'] . $bitrixUser['EMAIL']),
                        'is_default_password' => true,
                        'created_by' => 0,
                    ]);
                }
                // Insert or update user
                $user = User::updateOrCreate(
                    ['bitrix_user_id' => $bitrixUser['ID']],
                    $userData
                );
                UserProfile::updateOrCreate(
                    ['bitrix_user_id' => $bitrixUser['ID']],
                    [
                        'user_id' => $user->id,
                        'bitrix_name' => $bitrixUser['NAME'] ?? null,
                        'bitrix_last_name' => $bitrixUser['LAST_NAME'] ?? null,
                        'bitrix_second_name' => $bitrixUser['SECOND_NAME'] ?? null,
                        'bitrix_gender' => $bitrixUser['PERSONAL_GENDER'] ?? null,
                        'bitrix_profession' => $bitrixUser['PERSONAL_PROFESSION'] ?? null,
                        'bitrix_www' => $bitrixUser['PERSONAL_WWW'] ?? null,
                        'bitrix_birthday' => $bitrixUser['PERSONAL_BIRTHDAY'] ?? null,
                        'bitrix_profile_photo' => $bitrixUser['PERSONAL_PHOTO'] ?? null,
                        'bitrix_icq' => $bitrixUser['PERSONAL_ICQ'] ?? null,
                        'bitrix_phone' => $bitrixUser['PERSONAL_PHONE'] ?? null,
                        'bitrix_fax' => $bitrixUser['PERSONAL_FAX'] ?? null,
                        'bitrix_mobile' => $bitrixUser['PERSONAL_MOBILE'] ?? null,
                        'bitrix_pager' => $bitrixUser['PERSONAL_PAGER'] ?? null,
                        'bitrix_street' => $bitrixUser['PERSONAL_STREET'] ?? null,
                        'bitrix_city' => $bitrixUser['PERSONAL_CITY'] ?? null,
                        'bitrix_state' => $bitrixUser['PERSONAL_STATE'] ?? null,
                        'bitrix_zip' => $bitrixUser['PERSONAL_ZIP'] ?? null,
                        'bitrix_country_id' => $bitrixUser['PERSONAL_COUNTRY'] ?? null,
                        'bitrix_work_company' => $bitrixUser['WORK_COMPANY'] ?? null,
                        'bitrix_work_position' => $bitrixUser['WORK_POSITION'] ?? null,
                        'bitrix_work_phone' => $bitrixUser['WORK_PHONE'] ?? null,
                        'bitrix_interests' => $bitrixUser['UF_INTERESTS'] ?? null,
                        'bitrix_skills' => $bitrixUser['UF_SKILLS'] ?? null,
                        'bitrix_web_sites' => $bitrixUser['UF_WEB_SITES'] ?? null,
                        'bitrix_xing_link' => $bitrixUser['UF_XING'] ?? null,
                        'bitrix_linkedin_link' => $bitrixUser['UF_LINKEDIN'] ?? null,
                        'bitrix_facebook_link' => $bitrixUser['UF_FACEBOOK'] ?? null,
                        'bitrix_twitter_link' => $bitrixUser['UF_TWITTER'] ?? null,
                        'bitrix_skype_link' => $bitrixUser['UF_SKYPE'] ?? null,
                        'bitrix_district' => $bitrixUser['UF_DISTRICT'] ?? null,
                        'bitrix_phone_inner' => $bitrixUser['UF_PHONE_INNER'] ?? null,
                        'bitrix_user_type' => $bitrixUser['USER_TYPE'] ?? null,
                        'updated_by' => 0,
                    ]
                );
            });
        }

        return count($bitrixUsers) . " users synced successfully.";
    }
}
