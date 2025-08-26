<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'company_id';
    protected $fillable = [
        'company_id',
        'name',
        'license_number',
        'incorporation_date',
        'license_expiry_date',
        'authority',
        'organization_type',
        'status',
        'company_activity',
        'website',
        'email',
        'contact_no',
        'registered_address',
        'office_no',
        'building_name',
        'po_box',
        'city',
        'country',
        'annual_turnover',
        'logo',
    ];

    protected $casts = [
        // 'license_expiry_date' => 'date',
        // 'annual_turnover' => 'decimal:2',
    ];

    /**
     * Get the contacts associated with the company.
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'company_contact')
                    ->withPivot('position', 'department', 'role', 'start_date', 'end_date', 'is_primary', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the bank accounts associated with the company.
     */
    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class, 'company_id', 'company_id');
    }

    /**
     * Get the documents associated with the company.
     */
    public function documents()
    {
        return $this->hasMany(CompanyDocument::class,'company_id', 'company_id');
    }

    /**
     * Get tax documents for the company.
     */
    public function getTaxDocuments()
    {
        return $this->documents()->where('type', 'tax_document')->get();
    }

    /**
     * Get the company logo document.
     */
    public function getLogo()
    {
        return $this->hasOne(CompanyDocument::class, 'company_id', 'company_id')
                ->where('type', 'logo');
    }

    /**
     * Get accounting documents for the company, optionally filtered by year.
     */
    public function getAccountingDocuments($year = null)
    {
        $query = $this->documents()->where('type', 'accounting');

        if ($year) {
            $query->where('year', $year);
        }

        return $query->get();
    }

    public function relation()
    {
        return $this->hasMany(ContactRelationship::class, 'owner_id', 'company_id');
    }
}
