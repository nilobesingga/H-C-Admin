<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
    protected $table = 'company_contact';
    protected $fillable = [
        'company_id',
        'contact_id',
        'position',
        'department',
        'role',
        'start_date',
        'end_date',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the company associated with the contact.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id','company_id');
    }

    /**
     * Get the contact associated with the company.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class,'contact_id', 'bitrix_contact_id');
    }
}
