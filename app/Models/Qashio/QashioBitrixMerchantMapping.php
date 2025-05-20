<?php

namespace App\Models\Qashio;

use Illuminate\Database\Eloquent\Model;

class QashioBitrixMerchantMapping extends Model
{
    protected $table = 'qashio_bitrix_merchant_mapping';
    protected $fillable = [
        'id', 'qashio_name', 'bitrix_company_id', 'bitrix_company_name', 'bitrix_contact_id', 'bitrix_contact_name', 'is_active', 'created_by',
        'updated_by',
    ];
}
