<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    protected $userService;
    public function __construct(UserServices $userService)
    {
        $this->userService = $userService;
    }
    public function index()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        dd($user);
    }
    public function getPurchaseInvoices()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
                        ->where('bitrix_list_id', 1)
                        ->whereNotNull('sage_company_code')
                        ->whereIn('category_id', $userCategoryIds)
                        ->distinct()
                        ->get();
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
                        ->where('bitrix_list_id', 1)
                        ->whereIn('category_id', $userCategoryIds)
                        ->distinct()
                        ->get();
        $bitrixBankTransferCompanyIds = DB::table('bitrix_lists_sage_companies_mapping')
                        ->select([
                            DB::raw('MAX(CASE WHEN bitrix_list_id = 1 THEN bitrix_category_id END) as purchase_invoice_company_id'),
                            DB::raw('MAX(CASE WHEN bitrix_list_id = 4 THEN bitrix_category_id END) as bank_transfer_company_id'),
                            'bitrix_category_name',
                        ])
                        ->groupBy('bitrix_category_name') // Only group by bitrix_category_name
                        ->get();
        if($user){
            $page = (Object) [
                'title' => 'Purchase Invoices',
                'identifier' => 'reports_purchase_invoices',
                'user' => $user,
                'bitrix_list_sage_companies' => $bitrixListSageCompanies,
                'bitrix_list_categories' => $bitrixListCategories,
                'bitrixBankTransferCompanyIds' => $bitrixBankTransferCompanyIds
            ];
            return view('reports.purchase_invoices', compact('page'));
        }
    }
    public function getCashRequests()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 2)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        if($user) {
            $page = (object)[
                'title' => 'Cash Reports',
                'identifier' => 'reports_cash_reports',
                'user' => $user,
                'bitrix_list_sage_companies' => $bitrixListSageCompanies,
                'bitrix_list_categories' => $bitrixListCategories
            ];
        }
        return view('reports.cash_requests', compact('page'));
    }
    public function getBankTransfers()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 4)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 4)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        if($user) {
            $page = (object)[
                'title' => 'Bank Transfers',
                'identifier' => 'reports_bank_transfers',
                'user' => $user,
                'bitrix_list_sage_companies' => $bitrixListSageCompanies,
                'bitrix_list_categories' => $bitrixListCategories
            ];
        }
        return view('reports.bank_transfers', compact('page'));
    }
    public function getSalesInvoices()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 3)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 3)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        if($user) {
            $page = (object)[
                'title' => 'Sales Invoices',
                'identifier' => 'reports_sales_invoices',
                'user' => $user,
                'bitrix_list_sage_companies' => $bitrixListSageCompanies,
                'bitrix_list_categories' => $bitrixListCategories
            ];
        }
        return view('reports.sales_invoices', compact('page'));
    }
    public function getProformaInvoices()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $bitrixListSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 5)
            ->whereNotNull('sage_company_code')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 5)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        if($user){
            $page = (Object) [
                'title' => 'Proforma Invoices',
                'identifier' => 'reports_proforma_invoices',
                'user' => $user,
                'bitrix_list_sage_companies' => $bitrixListSageCompanies,
                'bitrix_list_categories' => $bitrixListCategories,
            ];
            return view('reports.proforma_invoices', compact('page'));
        }
    }
    public function getBankSummary()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $sageCompanyCode = BitrixListsSageCompanyMapping::select('category_id', 'sage_company_code', 'bitrix_sage_company_name')
            ->whereIn('category_id', $userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->distinct()
            ->get();

        if($user) {
            $page = (object)[
                'title' => 'Bank Summary',
                'identifier' => 'reports_bank_summary',
                'user' => $user,
                'sage_companies_code' => $sageCompanyCode,
            ];
        }
        return view('reports.bank_summary', compact('page'));
    }
    public function getExpensePlanner()
    {
        $user = User::with(['modules', 'categories'])->whereId(Auth::id())->first();
        $userCategoryIds = $user->categories->pluck('id');
        $categories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_category_name')
            ->whereIn('category_id', $userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->distinct()
            ->get();
        $sageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_sage_company_name', 'sage_company_code')
            ->whereIn('category_id', $userCategoryIds)
            ->whereNotNull('sage_company_code')
            ->distinct()
            ->get();
        $bitrixListPurchaseInvoicesSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 1)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListPurchaseInvoicesCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 1)
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCashRequestSageCompanies = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'sage_company_code', 'bitrix_sage_company_id', 'bitrix_sage_company_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        $bitrixListCashRequestCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 2)
            ->whereNotNull('bitrix_sage_company_id')
            ->whereIn('category_id', $userCategoryIds)
            ->distinct()
            ->get();
        if($user) {
            $page = (object)[
                'title' => 'Expense Planner',
                'identifier' => 'expense_planner',
                'user' => $user,
                'categories' => $categories,
                'sage_companies' => $sageCompanies,
                'bitrix_list_purchase_invoices_sage_companies' => $bitrixListPurchaseInvoicesSageCompanies,
                'bitrix_list_purchase_invoices_categories' => $bitrixListPurchaseInvoicesCategories,
                'bitrix_list_cash_requests_sage_companies' => $bitrixListCashRequestSageCompanies,
                'bitrix_list_cash_requests_categories' => $bitrixListCashRequestCategories
            ];
        }
        return view('reports.expense_planner', compact('page'));
    }
}
