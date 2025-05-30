<?php

namespace App\Http\Controllers\Ziina;
use App\Http\Controllers\Controller;

use App\Models\Ziina\ZiinaPayment;
use App\Models\Ziina\ZiinaPaymentLog;
use App\Services\Payment\ZiinaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZiinaWebhookController extends Controller
{
    protected $ziinaService;

    public function __construct(ZiinaPaymentService $ziinaService)
    {
        $this->ziinaService = $ziinaService;
    }
    public function updateStatus($invoice_id)
    {
        DB::beginTransaction();
        try {
            if(!isset($invoice_id)) {
                return response()->json(['error' => 'Invoice ID is required'], 400);
            }

            $invoice_id = decrypt($invoice_id);
            $payment = ZiinaPayment::where('invoice_id', $invoice_id)->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Check payment status from Ziina
            $paymentStatus = $this->ziinaService->checkPaymentStatus($payment->payment_id);
            if (!$paymentStatus) {
                return response()->json(['error' => 'Failed to check payment status'], 500);
            }

            // Update payment record
            $data = [
                'status' => $paymentStatus['status']
            ];
            if ($paymentStatus['status'] === 'completed') {
                // $this->ziinaService->updateBitrixInvoiceStatus($invoice_id);
                $this->ziinaService->createBitrixDealTask($payment->deal_id, 'Paid via Ziina', 'Payment for invoice : ' . $payment->invoice_number);
                $data['payment_completed_at'] = now();
            } elseif ($paymentStatus['status'] === 'failed') {
                $data['latest_error'] = $paymentStatus['latest_error'];
                $paymentStatus['status'] = 'payment failed';
            }
            $payment->update($data);

            // Log the status update
            ZiinaPaymentLog::create([
                'invoice_id' => $invoice_id,
                'payment_id' => $payment->payment_id,
                'account_id' => $payment->account_id,
                'payment_link' => $payment->payment_link,
                'operation_id' => $payment->operation_id,
                'status' => $paymentStatus['status'],
                'currency' => $payment->currency,
                'amount' => $payment->amount,
                'latest_error' => $paymentStatus['latest_error'] ?? null,
            ]);
            // Redirect to the redirect_url
            DB::commit();
            return redirect($paymentStatus['redirect_url']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment status update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update payment status'], 400);
        }
    }

    public function handlePaymentLogs($invoice_id)
    {
        if (!$invoice_id) {
            return response()->json(['error' => 'Invoice ID is required'], 400);
        }
        $payment = ZiinaPayment::with('paymentLogs')->where('invoice_id', $invoice_id)->first();
        if (!$payment) {
            return response()->json(['success' => 'Payment not found', 'data' => []], 200);
        }
        return response()->json(['message' => 'Payment logs.', 'data' => $payment], 200);
    }
    public function PaymentStatus()
    {
        $payment = ZiinaPayment::select('status','invoice_id','payment_completed_at','updated_at')->get();
        if (!$payment) {
            return response()->json(['error' => 'Payment not found','data' => []], 200);
        }
        foreach ($payment as &$pay) {
            $diff = $pay->updated_at->diff(now());
            $pay->counter = 'Sent '. sprintf(
                '%d days %d hrs %d min',
                $diff->days,
                $diff->h,
                $diff->i
            );
        }
        return response()->json(['message' => 'Payment Status', 'data' => $payment], 200);
    }
}
