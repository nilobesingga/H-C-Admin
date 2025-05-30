<?php
namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait EmailCategoryTrait
{
    protected function getEmailConfig(int $categoryId): array
    {
        $categoryConfig = Config::get('mail.category.' . $categoryId, []);

        if (empty($categoryConfig['address'])) {
            return Config::get('mail.from');
        }

        return [
            'address' => $categoryConfig['address'],
            'name' => $categoryConfig['name']
        ];
    }
}
