<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixList;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
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
    public function bitrixSageMapping()
    {
        $page = (Object) [
            'title' => 'Bitrix Sage Mapping',
            'identifier' => 'admin_settings_bitrix_sage_mapping',
            'categories' => Category::select('id', 'name')->get(),
            'bitrix_lists' => BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')->get(),
        ];

        return view('admin.settings.bitrix_sage_mapping', compact('page'));
    }
    public function bitrixSageMappingGetData($id)
    {
        try {
            if($id){
                dd('from bitrixSageMappingGetData', $id);
            }
            else {
                $filters = request('filters');

                $query = BitrixListsSageCompanyMapping::select('id', 'category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name', 'bitrix_category_id', 'bitrix_category_name');

                // category filter
                if (!empty($filters['category_id'])) {
                    $query->where('category_id', $filters['category_id']);
                }
                // bitrix list filter
                if (!empty($filters['bitrix_list_id'])) {
                    $query->where('bitrix_list_id', $filters['bitrix_list_id']);
                }

                return $query->get();
            }

        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function bitrixSageMappingSave(Request $request)
    {
        try {
            $requestData = $request->request_data;

            if ($request->form_type === 'add'){
                $obj = new BitrixListsSageCompanyMapping();
                $obj->category_id = $requestData['category_id'];
                $obj->bitrix_list_id = $requestData['bitrix_list_id'];
                $obj->sage_company_code = $requestData['sage_company_code'];
                $obj->bitrix_sage_company_id = $requestData['bitrix_sage_company_id'];
                $obj->bitrix_sage_company_name = $requestData['bitrix_sage_company_name'];
                $obj->bitrix_category_id = $requestData['bitrix_category_id'];
                $obj->bitrix_category_name = $requestData['bitrix_category_name'];

                $obj->save();

                return $this->successResponse('Data save successfully', null, 201);
            }
            if ($request->form_type === 'edit'){

            }

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }
}
