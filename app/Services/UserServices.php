<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class UserServices
{
    public function getAuthUserModules()
    {
        if(Auth::check()){
            $user = Auth::user();
            return $user->modules();
        }
    }
    public function getAuthUserCategories()
    {
        if(Auth::check()){
            $user = Auth::user();
            return $user->categories();
        }
    }
    public function getAuthUserModulesAndCategories()
    {
        if(Auth::check()){
            $user =  Auth::user();
            return $user->load([
                'modules' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'categories'
            ]);
        }
    }
    public function getUserCategoryIds()
    {
        if(Auth::check()){
            $user =  Auth::user();
            return $user->categories->pluck('id');
        }
    }
    public function getModuleBySlug($moduleSlug)
    {
        if($moduleSlug){
            return Module::whereSlug($moduleSlug)->first();
        }
    }

}
