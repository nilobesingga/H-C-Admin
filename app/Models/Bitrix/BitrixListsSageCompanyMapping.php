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
    protected $appends = ['category', 'bitrix_list'];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function getCategoryAttribute()
    {
        if($this->category_id){
            return Category::where('id', $this->category_id)->select('name')->value('name');
        }
        return null;
    }
    public function getBitrixListAttribute()
    {
        if($this->bitrix_list_id){
            return BitrixList::where('id', $this->bitrix_list_id)->select('name')->value('name');
        }
        return null;
    }
}
