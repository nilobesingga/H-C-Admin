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
        return $this->hasMany(Module::class, 'parent_id', 'id')->with('children');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_module_permission')
            ->withPivot('permission')
            ->withTimestamps();
    }
    public function getRouteNameAttribute()
    {
        // If this module has children, use the first child's slug
        if ($this->children->isNotEmpty()) {
            $child = $this->children->first();
            if (str_contains($child->route, 'cash-pool')) {
                return 'cash-pool.' . $child->slug;
            } elseif (str_contains($child->route, 'qashio')) {
                return 'qashio.' . $child->slug;
            } elseif (str_contains($child->route, 'reports')) {
                return 'reports.' . $child->slug;
            }
        }

        // Otherwise, route the current module
        if (str_contains($this->route, 'cash-pool')) {
            return 'cash-pool.' . $this->slug;
        } elseif (str_contains($this->route, 'qashio')) {
            return 'qashio.' . $this->slug;
        } elseif (str_contains($this->route, 'reports')) {
            return 'reports.' . $this->slug;
        }

        // Optional: fallback to slug as-is if no match
        return $this->slug;
    }

}
