<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    protected $table = 'user_module_permission';
    protected $fillable = ['user_id', 'module_id', 'permission'];
}
