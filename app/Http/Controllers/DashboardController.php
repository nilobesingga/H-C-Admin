<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = User::with([
            'modules' => function ($q) {
                $q->where('parent_id', 0)->orderBy('order', 'ASC');
            },
            'modules.children' => function ($q) {
                // Inner join to include only user-assigned child modules
                $q->join('user_module_permission as ump', function ($join) {
                    $join->on('modules.id', '=', 'ump.module_id')
                        ->where('ump.user_id', Auth::id());
                })
                ->orderBy('modules.order', 'ASC'); // Fallback to module order
            },
            'categories'
        ])->whereId(Auth::id())->first();
        $profile = Contact::where('contact_id', Auth::user()->bitrix_contact_id)->first();
        $profile['shortName'] = ($profile->name) ? getFirstChars($profile->name, true) : '';
        $profile['profilePhoto'] = ($profile->photo) ? Storage::url($profile->photo) : '/storage/images/logos/CRESCO_icon.png';
        $profile['resume'] =($profile->cv_file) ? Storage::url($profile->cv_file) : null;
        $profile['birthdate'] = date('Y-m-d', strtotime($profile->birthdate)) ?? null;
        $profile['dob'] = date('d M Y', strtotime($profile->birthdate)) ?? null;
        $profile['taxNo'] = $profile->tin ?? null;
        $contact_id = $profile->contact_id;
        $companies = Company::with(['getLogo','relation'])->whereIn('company_id', function ($query) use ($contact_id) {
                        $query->select('company_id')
                            ->from('company_contact')
                            ->where('contact_id', $contact_id);
                    })
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->map(function ($company) use($contact_id){
                    $otherRoles = $company->relation->where('owner_id_reverse', $contact_id)->toArray() ?? [];
                    return [
                        'id' => $company->company_id,
                        'logo' => $company['getLogo'] ? Storage::url($company['getLogo']['path']) : null,
                        'name' => $company->name,
                        'link' => "/company/{$company->company_id}",
                        'role' => null,
                        'otherRoles' =>  is_array($otherRoles) ? array_values($otherRoles) : [],
                        'tasks' => $company->tasks_count ?? 0,
                        'payments' => $company->payments_count ?? 0
                    ];
            })->toArray();
        $kycDocuments = [
            [
                "type" => "passport",
                "label" => "Passport",
                "fields" => [
                    ["label" => "Passport No", "value" => $profile->passport_number ?? null, "copy" => true],
                    ["label" => "Expiry Date", "value" => date('d M Y', strtotime($profile->passport_expiry_date)) ?? null, "copy" => false]
                ],
                "path" => $profile->passport_file ? Storage::url($profile->passport_file) : null,
                "download" => $profile->passport_file ? true : false
            ],
            [
                "type" => "uae_visa",
                "label" => "UAE Visa",
                "fields" => [
                    ["label" => "Visa No", "value" => $profile->residence_visa_file_number ?? null, "copy" => true],
                    ["label" => "Visa Expiry", "value" => date('d M Y', strtotime($profile->residence_visa_expiry_date)) ?? null, "copy" => false]
                ],
                "path" => $profile->residence_visa_file ? Storage::url($profile->residence_visa_file) : null,
                "download" => $profile->residence_visa_file ? true : false
            ],
            [
                "type" => "emirates_id",
                "label" => "Emirates ID",
                "fields" => [
                    ["label" => "Emirates ID No", "value" => $profile->emirates_id_number ?? null, "copy" => true],
                    ["label" => "Emirates Expiry", "value" => date('d M Y', strtotime($profile->emirates_id_expiry_date)) ?? null, "copy" => false]
                ],
                "path" => $profile->emirates_id_file ? Storage::url($profile->emirates_id_file) : null,
                "download" => $profile->emirates_id_file ? true : false
            ]
        ];
        $tasks = [
            [
                "id" => 1,
                "class" => "p-1.5 mb-2 border-l-2 border-green-600",
                "buttonClass" => "mb-2 text-green-700 bg-green-50 btn-sm",
                "company" => "CrescoPower",
                "text" => "Contract has been uploaded, review and sign using doc-sign."
            ],
            [
                "id" => 2,
                "class" => "p-1.5 border-l-2 border-blue-900",
                "buttonClass" => "mb-2 text-blue-900 bg-blue-100 btn-sm",
                "company" => "Cap Lion Point",
                "text" => "Invoice #63537 due for payment - pay now"
            ]
        ];
        $slides = [
            "/img/slide/slider1.svg",
            "/img/slide/slider1.svg",
            "/img/slide/slider1.svg"
        ];

        if($user){
            $page = (Object) [
                'title' => 'Dashboard',
                'identifier' => 'dashboard',
                'user' => $user
            ];
            return view('dashboard', compact('page', 'profile', 'companies', 'kycDocuments', 'tasks', 'slides'));
        }
    }

    /**
     * Update user personal details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePersonalDetails(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'phone' => 'required|string|max:20',
                'birthdate' => 'required|date',
                'nationality' => 'nullable|string|max:100',
                'taxNo' => 'nullable|string|max:50',
                'address' => 'nullable|string',
            ]);

            // Find the contact record
            $contact = Contact::where('contact_id', $request->contact_id)->first();

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact record not found'
                ], 404);
            }

            // Update contact details
            $contact->phone_no = $validatedData['phone'];
            $contact->birthdate = $validatedData['birthdate'];
            $contact->nationality = $validatedData['nationality'] ?? $contact->nationality;
            $contact->tin = $validatedData['taxNo'] ?? $contact->tin;
            $contact->address = $validatedData['address'] ?? $contact->address;
            // Save the changes
            $contact->save();
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Personal details updated successfully',
                'data' => [
                    'birthdate' => $contact->birthdate,
                    'nationality' => $contact->nationality,
                    'taxNo' => $contact->tin,
                    'address' => $contact->address
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating personal details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
