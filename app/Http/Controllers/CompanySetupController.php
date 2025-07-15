<?php

namespace App\Http\Controllers;

use App\Models\CompanySetup;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class CompanySetupController extends Controller
{
    use ApiResponser;

    /**
     * Store a new company setup request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string',
            'timeframe' => 'required|string',
            'language' => 'required|string',
            'contact_method' => 'nullable|string',
            'contact_id' => 'nullable|exists:contacts,contact_id',
        ]);

        $companySetup = CompanySetup::create($validatedData);

        return $this->successResponse( ['status', 'success', 'message' => 'Company setup request submitted successfully', $companySetup], 201);
    }
}
