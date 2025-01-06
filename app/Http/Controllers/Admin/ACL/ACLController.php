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
            DB::table('user_module_category')
                ->where('user_id', $userId)
                ->where('module_id', 1)
                ->delete();

            // Insert the new records
            foreach ($request['category_ids'] as $category_id) {
                DB::table('user_module_category')->insert([
                    'user_id' => $userId,
                    'module_id' => 1,
                    'category_id' => $category_id,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            return response()->json(['message' => 'User module and categories updated successfully'], 200);

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
