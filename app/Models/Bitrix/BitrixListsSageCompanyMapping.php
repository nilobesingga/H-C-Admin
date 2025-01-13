<?php

namespace App\Models\Bitrix;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class BitrixListsSageCompanyMapping extends Model
{
    protected $table = 'bitrix_lists_sage_companies_mapping';

    protected $fillable = [
        'category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name', 'bitrix_category_id',
        'bitrix_category_name', 'created_by', 'updated_by'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
