<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\Country;
use App\Models\Category;
use App\Models\Module;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSettingsController extends Controller
{
    use ApiResponser;
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
        $data = Category::select('id', 'name')
            ->with(['sageCompanies' => function ($q) {
                $q->selectRaw('MIN(id) as id, category_id, sage_company_code, bitrix_sage_company_name')
                    ->whereNotNull('sage_company_code')
                    ->groupBy('category_id', 'sage_company_code', 'bitrix_sage_company_name');
            }])
            ->get();


        $page = (Object) [
            'title' => 'Categories',
            'identifier' => 'admin_settings_categories',
            'data' => $data,
        ];

        return view('admin.settings.categories', compact('page'));
    }
    public function modules()
    {
        $page = (Object) [
            'title' => 'Modules',
            'identifier' => 'admin_settings_modules',
        ];

        return view('admin.settings.modules', compact('page'));
    }
    public function moduleGetData()
    {
        try {
            $filters = request('filters');

            $query = Module::with('parent')->orderBy('order');

            // Apply search filter
            if (!empty($filters['search'])) {
                $query->where('name', 'LIKE', '%'. $filters['search'] . "%");
            }

            return $query->get();

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function modulesOrderUpdate(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->all() as $item) {
                Module::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            DB::commit();
            return $this->successResponse('Module Order updated successfully');

        } catch (\Exception $e){
            DB::rollBack();
            return $this->errorResponse('Oops! An error occurred. Please refresh the page or contact support', config('app.debug') === true ? $e->getMessage() : null, 500 );
        }

    }
}
