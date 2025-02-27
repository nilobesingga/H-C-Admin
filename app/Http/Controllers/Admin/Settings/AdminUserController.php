<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserModulePermission;
use App\Services\Bitrix\BitrixService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    use ApiResponser;
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

            $query = User::with('profile', 'categories', 'modules')
                ->orderByRaw('LOWER(user_name) ASC');

            if (isset($filters['bitrix_active'])) {
                $query->where('bitrix_active', $filters['bitrix_active']);
            }

            // Apply search filter
            if (!empty($filters['search'])) {
                $search = strtolower($filters['search']);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(email) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(user_name) LIKE ?', ["%$search%"])
                        ->orWhereHas('profile', function ($q) use ($search) {
                            $q->whereRaw('LOWER(bitrix_name) LIKE ?', ["%$search%"])
                                ->orWhereRaw('LOWER(bitrix_last_name) LIKE ?', ["%$search%"])
                                ->orWhereRaw('LOWER(bitrix_second_name) LIKE ?', ["%$search%"]);
                        });
                });
            }

            return $query->get();

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function save(Request $request, $userId)
    {
        try {
            DB::beginTransaction();

            DB::table('category_user')
                ->where('user_id', $userId)
                ->delete();

            if (!empty($request['selected_category_ids'])) {
                foreach ($request['selected_category_ids'] as $categoryId) {
                    DB::table('category_user')->insert([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                    ]);
                }
            }

            // Handle module permissions
            DB::table('user_module_permission')
                ->where('user_id', $userId)
                ->delete();

            if (!empty($request['selected_modules'])) {
                foreach ($request['selected_modules'] as $module) {
                    DB::table('user_module_permission')->insert([
                        'user_id' => $userId,
                        'module_id' => $module['module_id'],
                        'permission' => $module['permission'],
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return $this->successResponse('User updated successfully');

        } catch (\Exception $e){
            DB::rollBack();
            return $this->errorResponse('Oops! An error occurred. Please refresh the page or contact support', config('app.debug') === true ? $e->getMessage() : null, 500 );
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
