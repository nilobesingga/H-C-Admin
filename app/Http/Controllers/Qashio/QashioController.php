<?php

namespace App\Http\Controllers\Qashio;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\Qashio\QashioBitrixMerchantMapping;
use App\Models\Qashio\QashioTransaction;
use App\Models\User;
use App\Models\UserModulePermission;
use App\Repositories\QashioApiRepository;
use App\Services\QashioService;
use App\Services\UserServices;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QashioController extends Controller
{
    use ApiResponser;
    protected $qashioService;
    protected $userService;
    protected $user;
    protected $userCategoryIds;
    public function __construct(QashioService $qashioService, UserServices $userService)
    {
        $this->qashioService = $qashioService;
        $this->userService = $userService;
        $this->user = $userService->getAuthUserModulesAndCategories();
        $this->userCategoryIds = $userService->getUserCategoryIds();
    }
    public function adminIndex()
    {
        $page = (object) [
            'title' => 'Qashio Admin',
            'identifier' => 'qashio_admin',
            'user' => $this->user,
        ];

        return view('qashio.admin', compact('page'));
    }
    public function transactionsIndex()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 9)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 9)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('qashio-transactions')->id
        ])->value('permission');

        $page = (object)[
            'title' => 'Qashio Transactions',
            'identifier' => 'qashio_transactions',
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories,
        ];

        return view('qashio.transactions', compact('page'));
    }
    public function getData()
    {
        try {
            if(request('is_sync')){
                // Sync transactions from Qashio API to local database
                $this->qashioService->syncQashioTransactions();
            }

            $filters = request('filters');

            $from = $filters['from_date'];
            $to = $filters['to_date'];

            $query = QashioTransaction::where('transactionTime', '>=', $from)
                ->where('transactionTime', '<=', $to . 'T23:59:59')
                ->orderBy('transactionTime', 'DESC');

            $data = $query->get();

            return $this->successResponse('Data fetched successfully', $data, 200);

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function saveBitrixCashRequest(Request $request, $type)
    {
        try {
            if (($type === 'create' && $request['bitrix_cash_request_id'] === null && $request['transactionCategory'] === 'purchase') && ($request['clearingStatus'] === 'pending' || $request['clearingStatus'] === 'cleared')) {
                $bitrixId = $this->qashioService->createBitrixCashRequest('user', $request->all());
                if ($bitrixId){
                    QashioTransaction::where('qashioId', $request['qashioId' ])->update([
                       'bitrix_cash_request_id' => $bitrixId,
                    ]);
                }
                return $this->successResponse('Cash Requisition created successfully', null, 201);
            }

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function getMerchantsData()
    {
        try {

            $data = QashioBitrixMerchantMapping::all();

            return $this->successResponse('Data fetched successfully', $data, 200);

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
}
