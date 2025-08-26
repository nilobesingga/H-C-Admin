<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\RequestFile;
use App\Models\RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = RequestModel::with(['company', 'contact', 'requestFile'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name', 'asc')->get();
        $contacts = Contact::orderBy('name', 'asc')->get();

        return view('requests.create', compact('companies', 'contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'category' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'required|in:document_request,change_request',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('request_files', $filename, 'public');

            // Create request file
            $requestFile = RequestFile::create([
                'company_id' => $request->company_id,
                'filename' => $path,
                'type' => $request->type,
                'contact_id' => $request->contact_id,
                'created_by' => Auth::id(),
            ]);

            // Create request
            $requestModel = RequestModel::create([
                'company_id' => $request->company_id,
                'category' => $request->category,
                'contact_id' => $request->contact_id,
                'created_by' => Auth::id(),
                'request_file_id' => $requestFile->id,
            ]);

            return redirect()->route('requests.show', $requestModel->id)
                ->with('success', 'Request created successfully.');
        }

        return redirect()->back()
            ->with('error', 'Error uploading file.')
            ->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requestModel = RequestModel::with(['company', 'contact', 'creator', 'requestFile'])
            ->findOrFail($id);

        return view('requests.show', compact('requestModel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $requestModel = RequestModel::findOrFail($id);
        $companies = Company::orderBy('name', 'asc')->get();
        $contacts = Contact::orderBy('name', 'asc')->get();

        return view('requests.edit', compact('requestModel', 'companies', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'category' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'required_with:file|in:document_request,change_request',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $requestModel = RequestModel::findOrFail($id);

        // Update request model
        $requestModel->update([
            'company_id' => $request->company_id,
            'category' => $request->category,
            'contact_id' => $request->contact_id,
        ]);

        // Handle file update if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('request_files', $filename, 'public');

            // Get the current request file
            $requestFile = RequestFile::findOrFail($requestModel->request_file_id);

            // Delete the old file if it exists
            if (Storage::disk('public')->exists($requestFile->filename)) {
                Storage::disk('public')->delete($requestFile->filename);
            }

            // Update request file
            $requestFile->update([
                'filename' => $path,
                'type' => $request->type,
            ]);
        }

        return redirect()->route('requests.show', $requestModel->id)
            ->with('success', 'Request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $requestModel = RequestModel::findOrFail($id);
        $requestFile = RequestFile::findOrFail($requestModel->request_file_id);

        // Delete the file from storage
        if (Storage::disk('public')->exists($requestFile->filename)) {
            Storage::disk('public')->delete($requestFile->filename);
        }

        // Delete the request file and request model
        $requestFile->delete();
        $requestModel->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Request deleted successfully.');
    }

    /**
     * Store a document request via API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRequestApi(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'details' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Begin transaction to ensure all or nothing is saved
        DB::beginTransaction();

        try {
            // Create request record
            $request_no = generateRequestNo();
            $requestRecord = RequestModel::create([
                'request_no' => $request_no ?? null,
                'company_id' => $request->company_id ?? null,
                'contact_id' => $request->contact_id,
                'category' => $request->category,
                'description' => $request->details,
                'type' => $request->type ?? null,
                'status' => $request->status ?? 'pending',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('request_files', $filename, 'public');

                // Create request file record
                RequestFile::create([
                    'request_id' => $requestRecord->id,
                    'filename' => $filename,
                    'path' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Request created successfully',
                'request' => $requestRecord
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //update request status
    public function updateRequestStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,declined,cancelled',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $requestModel = RequestModel::findOrFail($id);
            $requestModel->status = $request->status;
            $requestModel->save();
            DB::commit();
            return response()->json(['message' => 'Request status updated successfully', 'request' => $requestModel], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update request status', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRequest(Request $request){
        $limit = $request->input('limit', '');
        $data =  RequestModel::with(['company', 'contact', 'creator', 'files'])
            ->orderBy('created_at', 'DESC')
            ->where('status', $request->input('status', 'pending'))
            ->limit($limit)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'company_id' => $req->company_id,
                    'company_name' => $req->company ? $req->company->name : 'N/A',
                    'contact_name' => $req->contact ? $req->contact->name : 'N/A',
                    'contact_photo' => $req->contact ? Storage::url($req->contact->photo) : null,
                    'request_no' => $req->request_no,
                    'type' => str_replace("_"," ", $req->type),
                    'description' => $req->description,
                    'category' => $req->category,
                    'created_by' => $req->created_by,
                    'updated_at' => date('Y-m-d',strtotime($req->updated_at)),
                    'created_at' => date('Y-m-d',strtotime($req->created_at)),
                    'status' => $req->status,
                    'files' => $req->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'file_name' => $file->filename,
                            'path' => Storage::url($file->path),
                        ];
                    })->toArray()
                ];
            });
        return response()->json($data);
    }
}
