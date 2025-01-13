<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';
    protected $fillable = [
        'id', 'parent_id', 'name', 'slug', 'route', 'icon', 'order', 'is_active', 'created_by', 'updated_by'
    ];
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id', 'id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_module_permission')
            ->withPivot('permission')
            ->withTimestamps();
    }

}
