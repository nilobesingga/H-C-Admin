<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $page = (Object) [
            'title' => 'Dashboard',
            'identifier' => 'admin_dashboard',
        ];

        return view('admin.dashboard', compact('page'));
    }
}
