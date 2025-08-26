<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetup;
use App\Models\RequestModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $data = getUserModule('Activity Stream');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.dashboard', compact('page', 'module'));
    }
    public function getTask()
    {
        $data = getUserModule('Admin Task');
        $page = $data['page'];
        $module = $data['module'];
        $user = Auth::user();
        return view('admin.page.task', compact('page', 'module', 'user'));
    }
    public function getDocumentRequest()
    {
        $data = getUserModule('Document Request');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        $data =  RequestModel::with(['company', 'contact', 'creator', 'files'])
            ->orderBy('created_at', 'DESC')
            ->where('type', 'document_request')
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
        return view('admin.page.document-request', compact('page', 'module','data'));
    }
    public function getChangeRequest()
    {
        $data = getUserModule('Change Request');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        $data =  RequestModel::with(['company', 'contact', 'creator', 'files'])
            ->orderBy('created_at', 'DESC')
            ->where('type', 'change_request')
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
        return view('admin.page.change-request', compact('page', 'module','data'));
    }
    public function getInbox()
    {
        $data = getUserModule('Admin Inbox');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.inbox', compact('page', 'module'));
    }
    public function getChat()
    {
        $data = getUserModule('Admin Inbox');
        $page = (Object) $data['page'];
        $module = (Object) $data['module'];
        return view('admin.page.chat', compact('page', 'module'));
    }
    public function getSetupRequest()
    {
        $data = getUserModule('Setup Request');
        $page = $data['page'];
        $module = $data['module'];
        $user = Auth::user();
        $data = CompanySetup::with(['contact'])
                ->get()
                ->map(function ($req) {
                    $req->contact_name = $req->contact ? $req->contact->name : 'N/A';
                    $req->contact_photo = $req->contact ? Storage::url($req->contact->photo) : null;
                    return $req;
                });
        return view('admin.page.setup-request', compact('page', 'module', 'user','data'));
    }

    public function getDataRequest()
    {
        $data = getUserModule('Change Request');
        $page = $data['page'];
        $module = $data['module'];
        $user = Auth::user();
        $data = \App\Models\ChangeRequest::with(['requestedBy'])
                ->orderBy('created_at', 'DESC')
                ->get()
                ->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'model_type' => $req->model_type,
                        'model_id' => $req->model_id,
                        'field_name' => $req->field_name,
                        'current_value' => $req->current_value,
                        'proposed_value' => $req->proposed_value,
                        'status' => $req->status,
                        'reason' => $req->reason,
                        'requested_by' => $req->requestedBy ? $req->requestedBy->name : 'N/A',
                        'created_at' => $req->created_at,
                        'updated_at' => $req->updated_at
                    ];
                });
        return view('admin.page.data-request', compact('page', 'module', 'user','data'));
    }

    public function profile()
    {
        $user = Auth::user();
        $page = (Object) [
            'title' => 'User Profile',
            'identifier' => 'admin_user_profile',
            'user' => $user,
        ];
        $data = getUserModule('User Profile');
        $module = $data['module'];
        return view('admin.page.user-profile', compact('page', 'module'));
    }
}
