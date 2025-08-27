<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserModulePermission;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    use ApiResponser;
    public function __construct()
    {
    }
    public function index()
    {
        $page = (Object) [
            'title' => 'Users',
            'identifier' => 'admin_settings_users',
        ];
        $data = getUserModule('Users');
        $module = $data['module'];
        return view('admin.settings.users', compact('page','module'));
    }
    public function getData()
    {
        try {
            $filters = request('filters');

            $query = User::with('profile', 'categories', 'modules')
                    ->orderByRaw('LOWER(user_name) ASC');

            // if (isset($filters['bitrix_active'])) {
            //     $query->where('bitrix_active', $filters['bitrix_active']);
            // }

            // Apply search filter
            if (!empty($filters['search'])) {
                $search = strtolower($filters['search']);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(email) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(user_name) LIKE ?', ["%$search%"])
                        ->orWhereHas('profile', function ($q) use ($search) {
                            $q->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
                                // ->orWhereRaw('LOWER(bitrix_last_name) LIKE ?', ["%$search%"])
                                // ->orWhereRaw('LOWER(bitrix_second_name) LIKE ?', ["%$search%"]);
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
            $user = User::findOrFail($userId);
            if($user->is_admin != 1){
                DB::table('company_contact')
                    ->where('contact_id', $user->bitrix_contact_id)
                    ->delete();

                if (!empty($request['selected_category_ids'])) {
                    foreach ($request['selected_category_ids'] as $company_id) {
                        DB::table('company_contact')->insert([
                            'contact_id' => $user->bitrix_contact_id,
                            'company_id' => $company_id,
                            'created_at' => now(),
                        ]);
                    }
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
        $categories = $user['categories']->pluck('company_id');

        $query = Module::with(['children' => function ($q) {
                        $q->orderBy('order', 'ASC');
                        }])
                        ->select('id', 'parent_id', 'name', 'slug', 'route', 'order')
                        ->where('parent_id', 0);
                        if($user->is_admin != 1){
                            $query->where('admin', 0);
                        }
        $modulesData = $query->get();
        $profile = [];
        if (empty($user->profile)) {
            $profile = [
                'photo' => 'images/logos/CRESCO_icon.png',
                'name' => $user['user_name'],
            ];
            $user = array_merge($user->toArray(), ['profile' => $profile]);
        }
        $company = Company::select('id','company_id', 'name')->orderBy('name','ASC')->get();
        try {
            $data = (Object)[
                'obj' => $user,
                'modules' => $modulesData,
                'categories' => $company->whereNotIn('company_id', $categories)->toArray(),
                'selected_modules' => $modules,
                'selected_category_ids' => $categories,
                'selected_company' => $company->whereIn('company_id', $categories)->toArray(),
            ];
            return response()->json($data);

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function create(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'password' => 'required|string|min:6',
            'is_admin' => 'boolean',
        ]);
        try {
            $user = new User();
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->is_admin = $request->is_admin ? 1 : 0;
            $user->access_token = Str::random(64);
            $user->is_default_password = false;
            $user->status =  'offline';
            $user->is_admin = ($request->user_type == 'admin') ? 1 : 0; // Default to active
            $user->bitrix_active = 1; // Default to active
            $user->bitrix_contact_id = 0; // Default to active
            $user->created_by = 0; // System created
            $user->type = $request->user_type ?? 'user'; // Default to active
            $user->save();
            // Optionally create profile
            $user->profile()->create([
                'name' => $request->name,
                'photo' => '/storage/images/logos/CRESCO_icon.png',
            ]);
            DB::commit();
            return response()->json(['message' => 'User created successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getUsers()
    {
        try {
            $filters = request('filters');
            $query = User::with('userprofile')
                    ->orderByRaw('LOWER(user_name) ASC');
            // Apply search filter
            if (!empty($filters['search'])) {
                $search = strtolower($filters['search']);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(email) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(user_name) LIKE ?', ["%$search%"])
                        ->orWhereHas('userprofile', function ($q) use ($search) {
                            $q->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
                        });
                });
            }
            return $query->get();

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
