<?php

namespace App\Http\Controllers\Qashio;

use App\Http\Controllers\Controller;
use App\Models\Qashio\QashioTransaction;
use App\Models\User;
use App\Repositories\QashioApiRepository;
use App\Services\QashioService;
use App\Services\UserServices;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class QashioController extends Controller
{
    use ApiResponser;
    protected $qashioService;
    protected $userService;
    protected $user;
    public function __construct(QashioService $qashioService, UserServices $userService)
    {
        $this->qashioService = $qashioService;
        $this->userService = $userService;
        $this->user = $userService->getAuthUserModulesAndCategories();
    }
    public function index()
    {
        $page = (object) [
            'title' => 'Qashio Transactions',
            'identifier' => 'qashio_transactions',
            'user' => $this->user,
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
                $bitrixId = $this->qashioService->createBitrixCashRequest($request->all());
                if ($bitrixId){
                    QashioTransaction::where('qashioId', $request['qashioId'])->update([
                       'bitrix_cash_request_id' => $bitrixId,
                    ]);
                }
                return $this->successResponse('Cash Requisition created successfully', null, 201);
            }

        } catch (\Exception $e){
            return $this->errorResponse('Something went wrong! Please contact IT.', env('APP_ENV') !== 'production' ? $e->getMessage() : null, 500);
        }
    }
}
