<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::with([
            'modules' => function ($q) {
                $q->where('parent_id', 0)->orderBy('order', 'ASC');
            },
            'modules.children' => function ($q) {
                // Inner join to include only user-assigned child modules
                $q->join('user_module_permission as ump', function ($join) {
                    $join->on('modules.id', '=', 'ump.module_id')
                        ->where('ump.user_id', Auth::id());
                })
                ->orderBy('modules.order', 'ASC'); // Fallback to module order
            },
            'categories'
        ])->whereId(Auth::id())->first();

        if($user){
            $page = (Object) [
                'title' => 'Dashboard',
                'identifier' => 'dashboard',
                'user' => $user
            ];
            return view('dashboard', compact('page'));
        }
    }
}
