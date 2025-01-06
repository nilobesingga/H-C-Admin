<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\Country;
use App\Models\Category;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $page = (Object) [
            'title' => 'Settings',
            'identifier' => 'admin_settings',
        ];

        return view('admin.settings.settings_main', compact('page'));
    }
    public function countries()
    {
        $data = Country::select('id', 'bitrix_id', 'bitrix_name')->get();
        $page = (Object) [
            'title' => 'Countries',
            'identifier' => 'admin_settings_countries',
            'data' => $data
        ];

        return view('admin.settings.countries', compact('page'));
    }
    public function categories()
    {
        $data = Category::select('id', 'parent_id', 'name')->get();
        $page = (Object) [
            'title' => 'Categories',
            'identifier' => 'admin_settings_categories',
            'data' => $data,
        ];

        return view('admin.settings.categories', compact('page'));
    }
}
