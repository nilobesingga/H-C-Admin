<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    protected $table = 'company_bank_accounts';
    protected $fillable = [
        'company_id',
        'name',
        'display_text',
        'account_number',
        'iban',
        'swift',
        'currency',
    ];

    protected $casts = [
        'display_text' => 'json',
    ];

    /**
     * Get the company that owns the bank account.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
