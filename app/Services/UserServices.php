<?php

namespace App\Services;

use App\Models\Module;
use App\Models\User;
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
//        return User::with([
//            'modules' => function ($q) {
//                $q->where('parent_id', 0)->orderBy('order', 'ASC');
//            },
//            'modules.children' => function ($q) {
//                // Inner join to include only user-assigned child modules
//                $q->join('user_module_permission as ump', function ($join) {
//                    $join->on('modules.id', '=', 'ump.module_id')
//                        ->where('ump.user_id', Auth::id());
//                })
//                    ->orderBy('modules.order', 'ASC'); // Fallback to module order
//            },
//            'categories'
//        ])->whereId(Auth::id())->first();

        if(Auth::check()){
            $user =  Auth::user();
            return $user->load([
                'modules' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'modules.children' => function ($q) {
                    // Inner join to include only user-assigned child modules
                    $q->join('user_module_permission as ump', function ($join) {
                        $join->on('modules.id', '=', 'ump.module_id')
                            ->where('ump.user_id', Auth::id());
                    })
                        ->orderBy('modules.order', 'ASC'); // Fallback to module order
                },
                'categories',
                'profile'
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
