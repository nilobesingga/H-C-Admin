<?php

namespace App\Services\Bitrix;

use App\Models\User;

class BitrixCredentialResolver
{
    public function resolveCredentials($userId = null)
    {
        if ($userId === null) {
            return [
                'base_url' => config('services.bitrix.base_url'),
                'user_id' => config('services.bitrix.admin_user_id'),
                'webhook_token' => config('services.bitrix.admin_webhook_token'),
            ];
        }

        $user = User::whereId($userId)->first();

        if (!$user->bitrix_user_id && !$user->bitrix_webhook_token) {
            throw new \Exception("Bitrix credentials not found for user ID {$userId}");
        }

        return [
            'base_url' => config('services.bitrix.base_url'),
            'user_id' => $user->bitrix_user_id,
            'webhook_token' => $user->webhook_token,
        ];
    }
}
