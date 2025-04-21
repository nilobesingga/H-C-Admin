<?php

namespace App\Http\Controllers;

use App\Services\Email\EmailService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvoiceEmailController extends Controller
{
    use ApiResponser;

    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * InvoiceEmailController constructor.
     *
     * @param EmailService $emailService
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send an invoice email with a PDF attachment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvoiceEmail(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'email',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric',
            'message' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF file
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        // Prepare invoice data
        $invoiceData = [
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'amount' => $request->amount,
            'message' => $request->message ?? null,
        ];

        // Handle PDF file attachment
        $pdfPath = null;
        if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
            // Store the PDF file in the storage
            $pdfFile = $request->file('pdf_file');
            $pdfName = 'invoice_' . $request->invoice_number . '_' . time() . '.pdf';
            $pdfPath = $pdfFile->storeAs('invoices', $pdfName, 'public');

            // Get the full storage path
            $pdfPath = Storage::disk('public')->path($pdfPath);
        }

        // Send the email with the EmailService
        $sent = $this->emailService->sendInvoiceEmail(
            $invoiceData,
            $request->subject,
            $request->recipients,
            $pdfPath
        );

        if ($sent) {
            return $this->successResponse('Invoice email sent successfully');
        }

        return $this->errorResponse('Failed to send invoice email', null, 500);
    }

    /**
     * Send an invoice email with an existing PDF file.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendExistingInvoiceEmail(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'email',
            'invoice_data' => 'required|array',
            'pdf_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        // Check if the PDF file exists
        if (!file_exists($request->pdf_path)) {
            return $this->errorResponse('PDF file not found', null, 404);
        }

        // Send the email with the EmailService
        $sent = $this->emailService->sendInvoiceEmail(
            $request->invoice_data,
            $request->subject,
            $request->recipients,
            $request->pdf_path
        );

        if ($sent) {
            return $this->successResponse('Invoice email sent successfully');
        }

        return $this->errorResponse('Failed to send invoice email', null, 500);
    }
}
