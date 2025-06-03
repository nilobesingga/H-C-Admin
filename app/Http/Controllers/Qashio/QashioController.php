<?php

namespace App\Http\Controllers\Qashio;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixList;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\Qashio\QashioBitrixMerchantMapping;
use App\Models\Qashio\QashioTransaction;
use App\Models\UserModulePermission;
use App\Services\QashioService;
use App\Services\UserServices;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

        $bitrixCashRequisitionCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_category_id')
            ->distinct()
            ->get();
        $bitrixCashRequisitionSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('sage_company_code')
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('qashio-transactions')->id
        ])->value('permission');

        $bitrixList = BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')
            ->whereId(2)->first();

        $page = (object)[
            'title' => 'Qashio Transactions',
            'identifier' => 'qashio_transactions',
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories,
            'bitrix_cash_requisition_categories' => $bitrixCashRequisitionCategories,
            'bitrix_cash_requisition_sage_companies' => $bitrixCashRequisitionSageCompanies,
            'bitrix_list' => $bitrixList,
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

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully',
                'transactions' => $data,
                'last_sync' => QashioTransaction::max('last_sync')
            ], 200);

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function saveBitrixCashRequest(Request $request)
    {
        try {
            Log::channel('qashio')->info('Creating Bitrix cash request', [
                'user' => Auth::user()->user_name
            ]);
            $result = $this->qashioService->createBitrixCashRequest($request);
            if ($result['success']) {
                Log::channel('qashio')->info('Bitrix cash request created successfully', [
                    'user' => Auth::user()->user_name,
                    'bitrix_id' => $result['bitrix_id'],
                ]);
                return $this->successResponse('Cash Requisition created successfully', null, 201);
            }

            Log::channel('qashio')->error('Failed to create Bitrix cash request', [
                'user' => Auth::user()->user_name,
                'result' => $result,
            ]);
            return $this->errorResponse('Failed to create cash request in Bitrix', null, 500);

        } catch (\Exception $e){
            Log::channel('qashio')->error('Exception while creating Bitrix cash request', [
                'user' => Auth::user()->user_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function linkQashioTransactionWithBitrix(Request $request, $bitrixCashRequestId)
    {
        try {
            $qashioId = $request['qashioId'];

            $updated = QashioTransaction::where('qashioId', $qashioId)
                ->update([
                    'bitrix_cash_request_id' => $bitrixCashRequestId
                ]);

            if ($updated) {
                Log::channel('qashio')->info('Successfully linked Qashio transaction with Bitrix cash request', [
                    'user' => Auth::user()->user_name,
                    'qashio_id' => $qashioId,
                    'bitrix_cash_request_id' => $bitrixCashRequestId,
                ]);
                return $this->successResponse('Successfully linked Qashio Transaction with Cash Request', null, 200);
            }

        } catch (\Exception $e){
            Log::channel('qashio')->error('Failed to link Qashio transaction with Bitrix cash request', [
                'user' => Auth::user()->user_name,
                'qashio_id' => $qashioId,
                'bitrix_cash_request_id' => $bitrixCashRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function getMerchantsData()
    {
        try {
            $query = QashioBitrixMerchantMapping::query();

            if (request('qashio_merchant_name')) {
                $query->where('qashio_name', 'LIKE', '%' . request('qashio_merchant_name') . '%');
            }

            if(!request('is_array')){
                $data = $query->first();
            }
            else {
                $data = $query->get();
            }

            return $this->successResponse('Data fetched successfully', $data, 200);

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
    public function getQashioLog(Request $request)
    {
        try {
            $logFile = storage_path('logs/qashio.log');

            if (!File::exists($logFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Qashio log file not found.',
                ], 404);
            }

            // Read last N lines (default 1000) to avoid loading huge files
            $lines = $request->query('lines', 1000);
            $lines = min(max((int)$lines, 1), 5000); // Limit between 1 and 5000

            // Read file efficiently
            $content = $this->getLastLines($logFile, $lines);
            $logs = array_filter(explode("\n", $content));

            return $this->successResponse('Fetch logs successfully', $logs);

        } catch (\Exception $e) {
            Log::channel('qashio')->error('Failed to read Qashio log file', [
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse('Failed to read log file.', $e->getMessage());
        }
    }
    private function getLastLines($filePath, $lines)
    {
        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);

        $content = '';
        while (!$file->eof()) {
            $content .= $file->fgets();
        }

        return $content;
    }
}
