<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZiinaPayment extends Model
{
    protected $table = 'ziina_payment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'payment_id',
        'account_id',
        'operation_id',
        'payment_link',
        'status',
        'currency',
        'amount',
        'message',
        'success_url',
        'cancel_url',
        'failure_url',
        'expiry',
        'created_by',
        'updated_by',
        'payment_completed_at',
        'latest_error',
        'invoice_id',
        'invoice_number',
        'invoice_date',
        'recipient_name',
        'recipient_email',
        'total_amount',
        'service_charge',
        'bank_code',
        'filename'
    ];

    protected $casts = [
        'latest_error' => 'array'
    ];

    public function paymentLogs()
    {
        return $this->hasMany(ZiinaPaymentLog::class, 'payment_id', 'payment_id');
    }
}
