<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'bitrix_countries';
    protected $fillable = [
        'bitrix_id', 'bitrix_name', 'is_active'
    ];
}
