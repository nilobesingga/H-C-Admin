<?php

namespace App\Http\Controllers\Admin\ACL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ACLController extends Controller
{
    public function index()
    {
        $page = (Object) [
            'title' => 'ACL',
            'identifier' => 'admin_acl',
        ];

        return view('admin.acl.acl_main', compact('page'));
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
            return response()->json(['message' => 'User updated successfully'], 200);

        } catch (\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
