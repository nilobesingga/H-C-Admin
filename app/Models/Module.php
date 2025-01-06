<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';
    protected $fillable = [
        'id', 'parent_id', 'name', 'route', 'icon', 'is_active'
    ];
    public function categories() {
        return $this->hasMany(Category::class);
    }
    public function users() {
        return $this->belongsToMany(User::class, 'user_module_category')
            ->withPivot('category_id')
            ->withTimestamps();
    }
}
