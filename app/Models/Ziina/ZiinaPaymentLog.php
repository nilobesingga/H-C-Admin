<?php

namespace App\Models\Ziina;

use Illuminate\Database\Eloquent\Model;

class ZiinaPaymentLog extends Model
{
    protected $table = 'ziina_payment_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'invoice_id',
        'payment_id',
        'account_id',
        'operation_id',
        'payment_link',
        'status',
        'currency',
        'amount',
        'latest_error'
    ];
    protected $casts = [
        'latest_error' => 'array'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
