<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Models\User;
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

            $userFields = [
                'bitrix_user_id' => 'string',
                'email' => 'string',
            ];
            $profileFields = [
                'bitrix_name' => 'string',
                'bitrix_last_name' => 'string',
            ];

            // Apply search filter
            if (!empty($filters['search'])) {
                $query->where('email', 'LIKE', '%'. $filters['search'] . "%");
//                $query->search($filters['search'], $userFields, ['profile' => $profileFields]);
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
        try {
            $data = (Object)[
                'obj' => User::with('profile')->whereId($id)->first(),
                'user_category_ids' => DB::table('user_module_category')->where('user_id', $id)->pluck('category_id'),
                'modules' => Module::all(),
                'categories' => Category::all(),
            ];

            return $data;

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
