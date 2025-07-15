<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $data = getUserModule('Admin Dashboard');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.dashboard', compact('page', 'module'));
    }
    public function getTask()
    {
        $data = getUserModule('Admin Task');
        $page = $data['page'];
        $module = $data['module'];
        $user = Auth::user();
        return view('admin.page.task', compact('page', 'module', 'user'));
    }
    public function getRequest()
    {
        $data = getUserModule('Admin Request');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.request', compact('page', 'module'));
    }
    public function getInbox()
    {
        $data = getUserModule('Admin Inbox');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.inbox', compact('page', 'module'));
    }
    public function profile()
    {
        $user = Auth::user();
        $page = (Object) [
            'title' => 'User Profile',
            'identifier' => 'admin_user_profile',
            'user' => $user,
        ];
        $data = getUserModule('User Profile');
        $module = $data['module'];
        return view('admin.page.user-profile', compact('page', 'module'));
    }
}
