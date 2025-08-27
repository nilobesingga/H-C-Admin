<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeRequestController extends Controller
{
    public function approve($id)
    {
        try {
            $request = ChangeRequest::findOrFail($id);

            if ($request->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been processed'
                ], 400);
            }

            $request->status = 'approved';
            $request->reviewed_by = Auth::id();
            $request->reviewed_at = now();
            $request->save();

            // Apply the changes to the actual model
            // $modelClass = $request->model_type;
            $model = Contact::where('contact_id', $request->model_id)->first();
            if ($model) {
                $model->{$request->field_name} = $request->proposed_value;
                $model->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request'
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            $request = ChangeRequest::findOrFail($id);

            if ($request->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been processed'
                ], 400);
            }

            $request->status = 'rejected';
            $request->reviewed_by = Auth::id();
            $request->reviewed_at = now();
            $request->save();

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request'
            ], 500);
        }
    }
}
