<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::orderBy('name', 'asc')->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_id' => 'required|string|unique:companies,company_id',
            'license_number' => 'nullable|string|max:100',
            'incorporation_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date',
            'authority' => 'nullable|string|max:100',
            'organization_type' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'company_activity' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'contact_no' => 'nullable|string|max:20',
            'registered_address' => 'nullable|string|max:500',
            'office_no' => 'nullable|string|max:50',
            'building_name' => 'nullable|string|max:255',
            'po_box' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'annual_turnover' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('logo');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoFilename = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->storeAs('companies/logos', $logoFilename, 'public');
            $data['logo'] = $logoFilename;
        }

        $company = Company::create($data);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Company created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        $contacts = $company->contacts()->paginate(10);

        return view('companies.show', compact('company', 'contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::findOrFail($id);
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_id' => 'required|string|unique:companies,company_id,' . $company->id,
            'license_number' => 'nullable|string|max:100',
            'incorporation_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date',
            'authority' => 'nullable|string|max:100',
            'organization_type' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'company_activity' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'contact_no' => 'nullable|string|max:20',
            'registered_address' => 'nullable|string|max:500',
            'office_no' => 'nullable|string|max:50',
            'building_name' => 'nullable|string|max:255',
            'po_box' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'annual_turnover' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('logo');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                Storage::disk('public')->delete('companies/logos/' . $company->logo);
            }

            $logoFile = $request->file('logo');
            $logoFilename = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->storeAs('companies/logos', $logoFilename, 'public');
            $data['logo'] = $logoFilename;
        }

        $company->update($data);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);

        // Delete logo if exists
        if ($company->logo) {
            Storage::disk('public')->delete('companies/logos/' . $company->logo);
        }

        // Delete the company (will automatically remove pivot entries due to onDelete cascade)
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully');
    }

    /**
     * Show the form to add a contact to a company.
     */
    public function addContact(string $id)
    {
        $company = Company::findOrFail($id);
        $contacts = Contact::whereNotIn('id', $company->contacts->pluck('id'))->get();

        return view('companies.add_contact', compact('company', 'contacts'));
    }

    /**
     * Attach a contact to a company.
     */
    public function attachContact(Request $request, string $id)
    {
        $company = Company::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if contact is already attached to avoid duplicates
        if (!$company->contacts()->where('contacts.id', $request->contact_id)->exists()) {
            $company->contacts()->attach($request->contact_id, [
                'position' => $request->position,
                'department' => $request->department,
                'role' => $request->role,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_primary' => $request->has('is_primary') ? true : false,
                'notes' => $request->notes,
            ]);
        }

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Contact added to company successfully');
    }

    /**
     * Show the form to edit a contact's relationship with a company.
     */
    public function editContactRelationship(string $companyId, string $contactId)
    {
        $company = Company::findOrFail($companyId);
        $contact = Contact::findOrFail($contactId);
        $pivotData = $company->contacts()->where('contacts.id', $contactId)->first()->pivot;

        return view('companies.edit_contact', compact('company', 'contact', 'pivotData'));
    }

    /**
     * Update a contact's relationship with a company.
     */
    public function updateContactRelationship(Request $request, string $companyId, string $contactId)
    {
        $company = Company::findOrFail($companyId);
        $contact = Contact::findOrFail($contactId);

        $validator = Validator::make($request->all(), [
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company->contacts()->updateExistingPivot($contactId, [
            'position' => $request->position,
            'department' => $request->department,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_primary' => $request->has('is_primary') ? true : false,
            'notes' => $request->notes,
        ]);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Contact relationship updated successfully');
    }

    /**
     * Remove a contact from a company.
     */
    public function detachContact(string $companyId, string $contactId)
    {
        $company = Company::findOrFail($companyId);
        $contact = Contact::findOrFail($contactId);

        $company->contacts()->detach($contactId);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Contact removed from company successfully');
    }
}
