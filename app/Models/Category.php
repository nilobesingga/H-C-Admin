<?php

namespace App\Models;

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
    public function module() {
        return $this->belongsTo(Module::class);
    }
    public function users() {
        return $this->belongsToMany(User::class, 'user_module_category')
            ->withPivot('module_id')
            ->withTimestamps();
    }
}
