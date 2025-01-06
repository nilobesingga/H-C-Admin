<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        if($user){
            $page = (Object) [
                'title' => 'Purchase Invoices',
                'identifier' => 'reports_purchase_invoices',
                'user' => $user
            ];
            return view('dashboard', compact('page'));
        }
    }
}
