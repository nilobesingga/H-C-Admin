<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserModulePermission;
use App\Services\Bitrix\BitrixService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    protected $bitrixService;
    public function __construct(BitrixService $bitrixService)
    {
        $this->bitrixService = $bitrixService;
    }
    public function index()
    {
        $page = (Object) [
            'title' => 'Users',
            'identifier' => 'admin_settings_users',
        ];

        return view('admin.settings.users', compact('page'));
    }
    public function getData()
    {
        try {
            if(request('is_sync')){
                $this->bitrixService->syncBitrixUsers();
            }

            $filters = request('filters');

            $query = User::with('profile');

            // Apply search filter
            if (!empty($filters['search'])) {
                $query->where('email', 'LIKE', '%'. $filters['search'] . "%");
            }

            if ($filters['bitrix_active'] != null){
                $query->where('bitrix_active', $filters['bitrix_active']);
            }

            return $query->get();

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
    public function edit($id)
    {
        $user = User::with(['profile', 'categories'])->findOrFail($id);
        // Extract pivot data (module_id and permission)
        $modules = $user->modules->map(function ($module) {
            return [
                'module_id' => $module->pivot->module_id,
                'permission' => $module->pivot->permission,
            ];
        });
        // Extract category IDs
        $categories = $user->categories->pluck('id');

        try {
            $data = (Object)[
                'obj' => $user,
                'modules' => Module::with('children')->select('id', 'parent_id', 'name', 'slug', 'route')->where('parent_id', 0)->get(),
                'categories' => Category::select('id', 'name')->get(),
                'selected_modules' => $modules,
                'selected_category_ids' => $categories
            ];

            return $data;

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
