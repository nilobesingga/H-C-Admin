<?php

namespace App\Http\Controllers\Ziina;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ziina\ZiinaPayment;
use App\Models\Ziina\ZiinaPaymentLog;
use App\Services\Email\EmailService;
use App\Services\Payment\ZiinaPaymentService;
use App\Traits\ApiResponser;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class InvoiceEmailController extends Controller
{
    use ApiResponser;

    /**
     * @var EmailService
     */
    protected $emailService;
    protected $ziinaPaymentService;

    /**
     * InvoiceEmailController constructor.
     *
     * @param EmailService $emailService
     */
    public function __construct(EmailService $emailService, ZiinaPaymentService $ziinaPaymentService)
    {
        $this->ziinaPaymentService = $ziinaPaymentService;
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
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'recipients' => 'required|array',
            'recipients.*' => 'email',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'message' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF file
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 200);
        }
        try {
            $payment = ZiinaPayment::where('invoice_id', $request->invoice_id)->first();
            if ($payment) {
                if ($payment->status === 'completed') {
                    return $this->errorResponse('Payment already completed', null, 200);
                }
            }
            //call createPaymentIntent method to generate payment link
            $paymentLinkResponse = $this->createPaymentIntent($request);
            $responseData = json_decode($paymentLinkResponse->getContent(), true);
            if ($responseData['status'] === 'error') {
                return $this->errorResponse('Failed to generate payment link', null, 200);
            }
            // Add the payment link to the invoice data
            $request->merge(['payment_link' => $responseData['data']['redirect_url']]);
            // Log the payment link for debugging
            // Log::info('Generated payment link: ' . $paymentLinkResponse);
            // Prepare invoice data
            $amount = $request->amount;
            $serviceFee = ($amount * 3) / 100;
            $totalAmount = $amount + $serviceFee;
            $invoiceData = [
                'title' => $request->title ?? 'Payment Invoice',
                'invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => date('d M Y', strtotime($request->invoice_date)),
                'amount' => number_format($request->amount,2),
                'total_amount' => number_format($totalAmount,2),
                'service_charge' => number_format($serviceFee,2),
                'currency' => $request->currency,
                'recipient_name' => $request->recipient_name ?? null,
                'message' => $request->message ?? null,
                'payment_link' => $request->payment_link ?? null,
            ];
            // Handle PDF file attachment
            $pdfPath = null;
            if ($request->hasFile('pdf_file')) {
                // Retrieve the uploaded file
                $pdfFile = $request->file('pdf_file');

                // Define the filename (with fallback)
                $pdfName = $request->filename
                    ?? 'invoice_' . ($request->invoice_number ?? 'unknown') . '_' . time() . '.pdf';

                // Store the file in the 'public/invoices' directory
                $storedPath = $pdfFile->storeAs('invoices', $pdfName, 'public');

                // Full local path for attaching to email
                $pdfPath = Storage::disk('public')->path($storedPath);

                // Public URL for preview in email
                $previewUrl = Storage::disk('public')->url($storedPath);
                $invoiceData['preview_url'] = $previewUrl;
                $info = $this->getCompany($request->category_id);
                $invoiceData['logo'] = $info->logo;
                $invoiceData['company'] = $info->company;

            }

            // Send the email with the EmailService
            $sent = $this->emailService->sendInvoiceEmail(
                $invoiceData,
                $request->subject .' - ' . $request->title,
                $request->recipients,
                $pdfPath
            );

            if ($sent) {
                return $this->successResponse('Invoice email sent successfully');
            }

            return $this->errorResponse('Failed to send invoice email', null, 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to send invoice email', $th->getMessage(), 500);
        }
    }

    public function getCompany($category){
        $logoPath = '';
        $company = '';
        switch ($category) {
            case 3845:
                $company = 'Abode Options';
                $logoPath = 'img/Logo/abode.png';
                break;
            case 1457:
                $company = 'Bawbawon Hospitality Group';
                $logoPath = 'img/Logo/BHG - Blue.svg';
                break;
            case 1452:
                $company ="Bincres";
                $logoPath = 'img/Logo/BINCRES.svg';
                break;
            case 868:
                $company ="Caplion Point";
                $logoPath = 'img/Logo/CLP.svg';
                break;
            case 865:
                $company ="CRESCO Accounting";
                $logoPath = 'img/Logo/CRESCO Accounting.svg';
                break;
            case 866:
                $company ="CRESCO Compliance";
                $logoPath = 'img/Logo/CRESCO_Compliance.png';
                break;
            case 867:
                $company ="CRESCO Holding";
                $logoPath = 'img/Logo/CRESCO Holding.svg';
                break;
            case 847:
                $company ="CRESCO Legal";
                $logoPath = 'img/Logo/CRESCO Legal.svg';
                break;
            case 1463:
                $company ="CRESCO Power";
                $logoPath = 'img/Logo/CRESCO Power.svg';
                break;
            case 861:
                $company ="CRESCOtec";
                $logoPath = 'img/Logo/CRESCOTec.svg';
                break;
            case 858:
                $company ="Hensley & Cook";
                $logoPath = 'img/Logo/Hensley&Cook.svg';
                break;
            case 3936:
                $company ="Lionsrock";
                $logoPath = 'img/Logo/Lionsrock.svg';
                break;
            case 856:
                $company ="SADIQA";
                $logoPath = 'img/Logo/SADIQA.svg';
                break;
            case 860:
                $company ="Smart Money";
                $logoPath = 'img/Logo/Smart Money.svg';
                break;
            default:
                $company = "CRESCO";
                $logoPath = 'img/Logo/Cresco.svg';
                break;
        }
        $obj = new stdClass();
        $obj->logo = $logoPath;
        $obj->company = $company;
        return $obj;
    }

    public function createPaymentIntent(Request $request)
    {
        // dd($request->all());
       DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:2',
                'currency' => 'required|string|size:3',
                'message' => 'nullable|string|max:255',
                'invoice_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation error', $validator->errors(), 422);
            }

            // Generate URLs
            $baseUrl = env('APP_URL');
            $encryptedId = encrypt($request->invoice_id);
            $successUrl = $baseUrl . '/ziina-webhook/'.$encryptedId;
            $cancelUrl = $baseUrl . '/ziina-webhook/'.$encryptedId;
            $failureUrl = $baseUrl . '/ziina-webhook/'.$encryptedId;
            $message = $request->message;
            $dt = new DateTime('+90 days');
            $expiry = $dt->getTimestamp() * 1000; // Convert to milliseconds
            //Total Amount && Service Fee 3 percent
            $amount = $request->amount;
            $serviceFee = ($amount * 3) / 100;
            $totalAmount = $amount + $serviceFee;
            // Create payment intent

            $result = ZiinaPayment::where('invoice_id', $request->invoice_id)->first();
            if(!$result){
                $result = $this->ziinaPaymentService->createPaymentIntent(
                    str_replace('.', '', number_format($totalAmount, 2, '.', '')),
                    $request->currency,
                    $message,
                    $successUrl,
                    $cancelUrl,
                    $failureUrl,
                    $expiry
                );
                if (!$result || !isset($result['id'])) {
                    return $this->errorResponse('Failed to create payment intent ', null, 500);
                }
            }
            else{
                $result = $result->toArray();
            }
            // Check if the payment intent creation was successful
            $counter = ZiinaPaymentLog::where('invoice_id', $request->invoice_id)->count();
            $count = $counter;
            if($counter >= 1){
                $count = ($count == 1) ? 1 :  $count;
            }

            $result['redirect_url'] = $result['redirect_url'] ?? $result['payment_link'] ?? null;
            $paymentData = [
                'invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'recipient_name' => $request->recipient_name,
                'recipient_email' => $request->recipients[0] ?? null,
                'payment_id' => isset($result['payment_id']) ? $result['payment_id'] : $result['id'],
                'account_id' => $result['account_id'],
                'operation_id' => $result['operation_id'],
                'payment_link' => $result['redirect_url'],
                'status' => ($counter >= 1) ? 'reminder '. $count : 'initiated',
                'currency' => $request->currency,
                'amount' => $request->amount,
                'service_charge' => $serviceFee ?? 0,
                'total_amount' => $totalAmount,
                'message' => $message,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'failure_url' => $failureUrl,
                'expiry' =>  $expiry,
                'created_by' => $request->bitrixUserId,
                'invoice_id' => $request->invoice_id,
                'deal' => $request->deal ?? null,
                'deal_id' => $request->deal_id ?? null,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => date('Y-m-d',strtotime($request->invoice_date)),
                'recipient_name' => $request->recipient_name,
                'recipient_email' => $request->recipients[0] ?? null,
                'bank_code' => $request->bank_code ?? null,
                'filename' => $request->filename ?? 'invoice_' . $request->invoice_number . '_' . time() . '.pdf'
            ];
            ZiinaPayment::UpdateOrcreate([
                'account_id' => $result['account_id'],
                'invoice_id' => $request->invoice_id
            ],$paymentData);
            // Log the payment intent creation
            ZiinaPaymentLog::create([
                'invoice_id' => $request->invoice_id,
                'payment_id' => $result['id'],
                'account_id' => $result['account_id'],
                'payment_link' => $result['redirect_url'],
                'operation_id' => $result['operation_id'],
                'status' => ($counter >= 1) ? 'reminder '. $count : 'initiated',
                'currency' => $request->currency,
                'amount' => $request->amount,
                'latest_error' => $result['latest_error'] ?? null,
            ]);

            DB::commit();
            return $this->successResponse('Payment intent created', $result);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment intent: ' . $e->getMessage());
            return $this->errorResponse('Failed to create payment intent', null, 500);
        }
    }

    public function getFile($filename)
    {
        // Validate filename to prevent directory traversal attacks
        if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return response()->json(['error' => 'Invalid filename'], 400);
        }

        // Determine storage path (adjust this path to match your storage structure)
        $path = storage_path('app/public/invoices/' . $filename);

        // Check if file exists
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Return file as download
        return response()->file($path);
    }
}
