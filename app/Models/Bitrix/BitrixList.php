<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;

class BitrixList extends Model
{
    protected $table = 'bitrix_lists';
    protected $fillable = [
        'name', 'bitrix_iblock_type', 'bitrix_iblock_id', 'is_active'
    ];
}
