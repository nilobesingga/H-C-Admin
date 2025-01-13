<?php

namespace App\Models;

use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'id', 'parent_id', 'name', 'is_active'
    ];
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
    public function sageCompanies()
    {
        return $this->hasMany(BitrixListsSageCompanyMapping::class, 'category_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
