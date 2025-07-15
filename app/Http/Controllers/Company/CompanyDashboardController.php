<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RequestModel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDashboardController extends Controller
{
    protected $companiesData = array();
    protected $selectedCompany = null;
    public function __construct()
    {
        $this->companiesData = $this->getCompaniesData();
        $this->selectedCompany = array();
    }
    public function index($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_list = $this->companiesData;
        if(empty($this->selectedCompany)){
            return redirect()->back()->withErrors('error', 'No companies found. Please contact your administrator.');
        }
        $company_data = $this->selectedCompany;
        $data = getUserModule('Modules', $company_id);
        $page = $data['page'];
        $module = $data['module'];
        return view('company.page.company', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function getRequest($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $data =  RequestModel::with(['company', 'contact', 'creator', 'requestFile'])
            ->where('contact_id', Auth::user()->bitrix_contact_id)
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'company_id' => $req->company_id,
                    'request_no' => $req->request_no,
                    'type' => str_replace("_"," ", $req->type),
                    'description' => $req->description,
                    'category' => $req->category,
                    'created_by' => $req->created_by,
                    'updated_at' => date('Y-m-d',strtotime($req->updated_at)),
                    'created_at' => date('Y-m-d',strtotime($req->created_at)),
                    'status' => $req->status,
                    'file' => $req->requestFile ? Storage::url($req->requestFile->filename) : null
                ];
            });
        $xdata = getUserModule('My Requests', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.request', compact('page','company_list', 'company_id','company_data','data','module'));

    }

    public function getInbox($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $xdata = getUserModule('Inbox', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.inbox', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function getPayment($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $xdata = getUserModule('Payments', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.payment', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function getWallet($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $xdata = getUserModule('Wallet', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.wallet', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function getTask($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $xdata = getUserModule('My Task', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.task', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function getCalendar($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;
        $xdata = getUserModule('Calendar', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.calendar', compact('page','company_list', 'company_id','company_data','module'));
    }

    public function switchCompany(Request $request, $company_id)
    {
        // Store the selected company in the session
        session(['selected_company' => $company_id]);

        // Always redirect to the company dashboard with the selected company
        return redirect()->route('company.index', ['company' => $company_id])->with('success', 'Company switched successfully');
    }

    /**
     * Get the currently selected company or default to 'Sadiqa'
     */
    private function getSelectedCompany()
    {
        $company_id = session('selected_company', 0);
        return array_values(array_filter($this->companiesData, function ($company) use ($company_id) {
            return $company['company_id'] == $company_id;
        }))[0] ?? [];
    }

    /**
     * Get companies data for the sidebar and company view
     */
    private function getCompaniesData()
    {
        // In a real application, these would come from the database
        $contact_id = Auth::user()->bitrix_contact_id;
        return Company::with(['getLogo','relation','bankAccounts','documents'])->whereIn('company_id', function ($query) use ($contact_id) {
                        $query->select('company_id')
                            ->from('company_contact')
                            ->where('contact_id', $contact_id);
                    })
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->map(function ($company){
                    $relation = $company->relation->where('owner_id', $company->company_id)->toArray() ?? [];
                    return [
                        'company_id' => $company->company_id,
                        'logo' => $company['getLogo'] ? Storage::url($company['getLogo']['path']) : null,
                        'name' => $company->name ?? '',
                        'status' => $company->status ?? '',
                        'company_activity' => $company->company_activity ?? 'Not Available',
                        'incorporation_date' => ($company->incorporation_date) ? date('d M Y',strtotime($company->incorporation_date)) : '',
                        'license_number' => $company->license_number ?? 'Not Available',
                        'license_expiry_date' => $company->license_expiry_date ?? 'Not Available',
                        'authority' => $company->authority ?? 'Not Available',
                        'organization_type' => $company->organization_type ?? 'Not Available',
                        'activity' => $company->activity ?? 'Not Available',
                        'website' => $company->website ?? 'Not Available',
                        'contact_no' => $company->contact_no ?? 'Not Available',
                        'email' => $company->email ?? 'Not Available',
                        'address' => $company->registered_address ?? 'Not Available',
                        'building' => $company->building_name ?? 'Not Available',
                        'po_box' => $company->po_box ?? 'Not Available',
                        'city' => $company->city ?? 'Not Available',
                        'country' => $company->country ?? 'Not Available',
                        'annual_turnover' => $company->annual_turnover ?? '$0.00',
                        'relation' => is_array($relation) ? array_values($relation) : [],
                        'bank' => $company->bankAccounts ?? [],
                        'documents' => $company->documents ?? []
                    ];
            })
            ->toArray();
    }

    /**
     * Quick Chat functionality for companies
     *
     * @param string|null $company
     * @return \Illuminate\Contracts\View\View
     */
    public function getQuickChat($company_id = null)
    {
        if ($company_id) {
            session(['selected_company' => $company_id]);
            $this->selectedCompany = $this->getSelectedCompany();
        }
        $company_data = $this->selectedCompany;
        $company_list = $this->companiesData;

        $xdata = getUserModule('Quick Chat', $company_id);
        $page = $xdata['page'];
        $module = $xdata['module'];
        return view('company.page.quickchat', compact('page','company_list', 'company_id','company_data','module'));
    }
}
