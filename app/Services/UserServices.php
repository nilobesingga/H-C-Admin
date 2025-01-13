<?php

namespace App\Services;

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
}
