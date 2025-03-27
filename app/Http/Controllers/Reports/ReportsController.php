<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixList;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\UserModulePermission;
use App\Repositories\BitrixApiRepository;
use App\Services\UserServices;
use App\Traits\ApiResponser;
use \Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use ApiResponser;
    protected $userService;
    protected $user;
    protected $userCategoryIds;
    public function __construct(UserServices $userService)
    {
        $this->userService = $userService;
        $this->user = $userService->getAuthUserModulesAndCategories();
        $this->userCategoryIds = $userService->getUserCategoryIds();
    }
    public function getPurchaseInvoices()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 1)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
                        ->where('bitrix_list_id', 1)
                        ->whereNotNull('sage_company_code')
                        ->whereIn('category_id', $this->userCategoryIds)
                        ->distinct()
                        ->get();

        $bitrixBankTransferCompanyIds = DB::table('bitrix_lists_sage_companies_mapping')
                        ->select([
                            DB::raw('MAX(CASE WHEN bitrix_list_id = 1 THEN bitrix_category_id END) as purchase_invoice_company_id'),
                            DB::raw('MAX(CASE WHEN bitrix_list_id = 2 THEN bitrix_category_id END) as cash_request_company_id'),
                            DB::raw('MAX(CASE WHEN bitrix_list_id = 4 THEN bitrix_category_id END) as bank_transfer_company_id'),
                            'bitrix_category_name',
                        ])
                        ->groupBy('bitrix_category_name')
                        ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('purchase-invoices')->id
        ])->value('permission');

        $bitrixList = BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')
                ->whereId(1)->first();

        $page = (Object) [
            'title' => 'Purchase Invoices',
            'identifier' => 'reports_purchase_invoices',
            'bitrix_list' => $bitrixList,
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories,
            'bitrix_bank_transfer_company_ids' => $bitrixBankTransferCompanyIds
        ];
        return view('reports.purchase_invoices', compact('page'));
    }
    public function getCashRequests()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixBankTransferCompanyIds = DB::table('bitrix_lists_sage_companies_mapping')
            ->select([
                DB::raw('MAX(CASE WHEN bitrix_list_id = 2 THEN bitrix_category_id END) as cash_request_company_id'),
                DB::raw('MAX(CASE WHEN bitrix_list_id = 4 THEN bitrix_category_id END) as bank_transfer_company_id'),
                'bitrix_category_name',
            ])
            ->groupBy('bitrix_category_name')
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('cash-requests')->id
        ])->value('permission');

        $bitrixList = BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')
            ->whereId(2)->first();

        $page = (object)[
            'title' => 'Cash Requests',
            'identifier' => 'reports_cash_reports',
            'bitrix_list' => $bitrixList,
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories,
            'bitrix_bank_transfer_company_ids' => $bitrixBankTransferCompanyIds
        ];
        return view('reports.cash_requests', compact('page'));
    }
    public function getBankTransfers()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 4)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 4)
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('bank-transfers')->id
        ])->value('permission');

        $bitrixList = BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')
            ->whereId(4)->first();

        $page = (object)[
            'title' => 'Bank Transfers',
            'identifier' => 'reports_bank_transfers',
            'bitrix_list' => $bitrixList,
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories
        ];
        return view('reports.bank_transfers', compact('page'));
    }
    public function getSalesInvoices()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 3)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 3)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('sales-invoices')->id
        ])->value('permission');

        $page = (object)[
            'title' => 'Sales Invoices',
            'identifier' => 'reports_sales_invoices',
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories
        ];

        return view('reports.sales_invoices', compact('page'));
    }
    public function getProformaInvoices()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 5)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 5)
            ->whereNotNull('sage_company_code')
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('proforma-invoices')->id
        ])->value('permission');

        $page = (Object) [
            'title' => 'Proforma Invoices',
            'identifier' => 'reports_proforma_invoices',
            'permission' => $modulePermission,
            'user' => $this->user,
            'bitrix_list_sage_companies' => $bitrixListSageCompanies,
            'bitrix_list_categories' => $bitrixListCategories,
        ];

        return view('reports.proforma_invoices', compact('page'));
    }
    public function getBankSummary()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 7)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $sageCompanyCode = BitrixListsSageCompanyMapping::select('category_id', 'sage_company_code', 'bitrix_sage_company_name')
            ->whereIn('category_id', $this->userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->distinct()
            ->orderBy('bitrix_sage_company_name')
            ->get();

        $bitrixSageCompanyMapping = BitrixListsSageCompanyMapping::whereIn('bitrix_list_id', ['1', '2', '3'])->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('bank-summary')->id
        ])->value('permission');

        $bitrixList = BitrixList::select('id', 'name', 'bitrix_iblock_type', 'bitrix_iblock_id')
            ->whereId(7)->first();

        // cheque register warning counts.
        $bitrixAPI = app(BitrixAPIRepository::class);
        $chequeRegisterWarningCounts = $bitrixAPI->call('crm.company.reports_v2', [
            'action' => "getChequeRegisterWarningCounts",
            'startDate' => getDateOFLast60Days(),
            'endDate' => getLastDateOfMonthAfterThreeYears(),
            'categories' => json_encode($bitrixListCategories->pluck('bitrix_category_id')->toArray()),
        ]);

        $page = (object)[
            'permission' => $modulePermission,
            'user' => $this->user,
            'warning_counts' => isset($chequeRegisterWarningCounts['result']) && is_array($chequeRegisterWarningCounts['result']) ? $chequeRegisterWarningCounts['result'] : null
        ];

        $section = request()->get('section');

        // Overview
        if ($section === 'overview'){
            $page->title = "Bank Summary | Overview";
            $page->identifier = "reports_bank_summary_overview";
            $page->sage_companies_code = $sageCompanyCode;
            $page->bitrix_sage_company_mapping = $bitrixSageCompanyMapping;
        // Cheque Register Outgoing
        }
        else if($section === 'cheque-register-outgoing'){
            $page->title = "Bank Summary | Cheque Register Outgoing";
            $page->identifier = "reports_bank_summary_cheque_register_outgoing";
            $page->bitrix_list = $bitrixList;
            $page->bitrix_list_categories = $bitrixListCategories;
        }
        // Cheque Register Incoming
        else if($section === 'cheque-register-incoming'){
            $page->title = "Bank Summary | Cheque Register Incoming";
            $page->identifier = "reports_bank_summary_cheque_register_incoming";
            $page->bitrix_list = $bitrixList;
            $page->bitrix_list_categories = $bitrixListCategories;
        }
        // Cash by Currency
        else if($section === 'cash-by-currency'){
            $page->title = "Bank Summary | Cash By Currency";
            $page->identifier = "reports_bank_summary_cash-by-currency";
            $page->sage_companies_code = $sageCompanyCode;
            $page->bitrix_sage_company_mapping = $bitrixSageCompanyMapping;
        }
        // redirect to overview
        else {
            return redirect()->route('reports.bank-summary', ['section' => 'overview']);
        }

        return view('reports.bank_summary', compact('page'));

    }
    public function getExpensePlanner()
    {
        $categories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_category_name')
            ->whereIn('category_id', $this->userCategoryIds)
            ->whereNotNull('bitrix_category_name')
            ->distinct()
            ->get();

        $sageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_sage_company_name', 'sage_company_code')
            ->whereIn('category_id', $this->userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->orderBy('category_id')
            ->get()
            ->unique('sage_company_code')
            ->values()
            ->toArray();

        $bitrixListPurchaseInvoicesSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 1)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListPurchaseInvoicesCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 1)
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCashRequestSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCashRequestCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('expense-planner')->id
        ])->value('permission');

        $page = (object)[
            'title' => 'Expense Planner',
            'identifier' => 'expense_planner',
            'permission' => $modulePermission,
            'user' => $this->user,
            'categories' => $categories,
            'sage_companies' => $sageCompanies,
            'bitrix_list_purchase_invoices_sage_companies' => $bitrixListPurchaseInvoicesSageCompanies,
            'bitrix_list_purchase_invoices_categories' => $bitrixListPurchaseInvoicesCategories,
            'bitrix_list_cash_requests_sage_companies' => $bitrixListCashRequestSageCompanies,
            'bitrix_list_cash_requests_categories' => $bitrixListCashRequestCategories
        ];
        return view('reports.expense_planner', compact('page'));
    }
    public function getBankMonitoring()
    {
        $sageCompanyCodes = BitrixListsSageCompanyMapping::select('category_id', 'sage_company_code', 'bitrix_sage_company_name')
            ->whereIn('category_id', $this->userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->orderBy('category_id')
            ->get()
            ->unique('sage_company_code')
            ->values()
            ->toArray();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('bank-monitoring')->id
        ])->value('permission');

        $page = (object)[
            'title' => 'Bank Monitoring',
            'identifier' => 'bank_monitoring',
            'permission' => $modulePermission,
            'user' => $this->user,
            'sage_companies_codes' => $sageCompanyCodes
        ];

        return view('reports.bank_monitoring', compact('page'));
    }
    public function getBankAccounts()
    {
        $sageCompanyCodes = BitrixListsSageCompanyMapping::select('category_id', 'sage_company_code', 'bitrix_sage_company_name')
            ->whereIn('category_id', $this->userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->orderBy('category_id')
            ->get()
            ->unique('sage_company_code')
            ->values()
            ->toArray();

        $modulePermission = UserModulePermission::where([
            'user_id' => Auth::id(),
            'module_id' => $this->userService->getModuleBySlug('bank-accounts')->id
        ])->value('permission');

        $page = (object)[
            'title' => 'Bank Accounts',
            'identifier' => 'bank_accounts',
            'permission' => $modulePermission,
            'user' => $this->user,
            'sage_companies_codes' => $sageCompanyCodes
        ];

        return view('reports.bank_accounts', compact('page'));
    }
    public function getCashPool()
    {
        $page = (object)[
            'title' => 'Cash Pool',
            'identifier' => 'Cash Pool',
            'user' => $this->user,
        ];

        return view('reports.cash_pool', compact('page'));
    }
    public function downloadCashReleasedReceipt(Request $request)
    {
        try {
            $logoPath = public_path('img/CRESCO_Holding.png');
            $logo = null;

            if (file_exists($logoPath)) {
                $sealBase64 = base64_encode(file_get_contents($logoPath));
                $sealMimeType = mime_content_type($logoPath);
                $logo = "data:$sealMimeType;base64,$sealBase64";
            }

            $data = [
                'logo' =>   $logo ,
                'requestId' => $request['requestId'] != '' ? $request['requestId'] : 'empty',
                'requestCreateDate' => $request['requestCreateDate'] != '' ? $request['requestCreateDate'] : 'empty',
                'requestPaymentDate' => $request['requestPaymentDate'] != '' ? $request['requestPaymentDate'] : 'empty',
                'releaseDate' => $request['releaseDate'] != '' ? $request['releaseDate'] : 'empty',
                'requestedBy' => $request['requestedBy'] != '' ? $request['requestedBy'] : 'empty',
                'releasedBy' => $request['releasedBy'] != '' ? $request['releasedBy'] : 'empty',
                'project' => $request['project'] != '' ? $request['project'] : 'No project specified',
                'company' =>  $request['company'] != '' ? $request['company'] : 'No company specified',
                'remarks' => $request['remarks'] != '' ? $request['remarks'] : 'No remarks',
                'amountReceived' => $request['amountReceived'] != '' ? $request['amountReceived'] : '0',
                'currency' => $request['currency'] != '' ? $request['currency'] : '',
                'cashReleaseType'  => $request['cashReleaseType'] != '' ? $request['cashReleaseType'] : 1,
                'balance'  => $request['balance'] != '' ? $request['balance'] : '',
                'paymentMode' => $request['paymentMode'] != '' ? $request['paymentMode'] : 'Cash',
            ];

            $templateName = 'templates/cash_release_receipt_template';

            $pdf = PDF::loadView($templateName, $data)
                ->setOption('enable-local-file-access', true);
            return $pdf->download('receipt.pdf');

        } catch (\Exception $e){
            return $this->errorResponse('Error while downloading cash release receipt', $e->getMessage());
        }
    }
    public function redirectToReports()
    {
        $path = request()->path();
        $prefix = str_starts_with($path, 'reports/') ? 'reports/' : 'cash-pool/';
        $slug = str_replace($prefix, '', $path);

        // Define reports that should be embedded using an iframe
        $iframeReports = [
            'cash-pool-report',
        ];

        $reportMapping = [
            'cresco-holding' => '/holding',
            'cresco-accounting' => '/accounting',
            'cresco-sage' => '/sage',
            'hensley-and-cook' => '/compliance',
            'orchidx' => '/orchid/banks',
            'expense-overview' => '/accounting/expensecalendar',
            'managing-director-reports' => '/md/mdPurchaseInvoices',
            'crm-relationships-report' => '/entity/relationships',
            'demo-reports' => '/demo/dbanks',
            'hr-reports' => '/hr/checkIn',
            'bank-reports' => '/cresco/banks/summary',
            'running-accounts' => '/cresco/runningAccount/runningbalances',
            'cash-pool-report' => '/cashpool',
        ];

        $user = Auth::user();
        $accessToken = $user->access_token;

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not found'], 403);
        }

        // Determine the correct base URL based on environment
        $baseUrl = match (env('APP_ENV')) {
            'staging' => env('CRESCO_REPORTS_STAGING_BASE_URL'),
            'production' => env('CRESCO_REPORTS_BASE_URL'),
            default => 'http://localhost:8000',
        };

        // Construct the authentication URL
        $authUrl = $baseUrl . '/bitrix/login/' . $accessToken;

        // After authentication, redirect to the requested report
        $redirectAfterLogin = urlencode($baseUrl . $reportMapping[$slug]);

        // Final redirect URL
        $finalUrl = $authUrl . '?redirect=' . $redirectAfterLogin;

        if (in_array($slug, $iframeReports)) {
            $page = (object)[
                'title' => 'Cash Pool',
                'identifier' => 'Cash Pool',
                'user' => $this->user,
            ];
//            return view('reports.embedded_report', compact('finalUrl'));
            return view('reports.cash_pool', compact('page', 'finalUrl'));
        }
        else {
            return redirect()->away($finalUrl);
        }
    }
}
