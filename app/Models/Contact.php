<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $fillable = [
        'contact_id',
        'name',
        'nationality',
        'birthdate',
        'cv_file',
        'photo',
        'mobile',
        'email',
        'phone_no',
        'tin',
        'passport_number',
        'passport_place_of_issue',
        'passport_issue_date',
        'passport_expiry_date',
        'passport_file',
        'residence_visa_number',
        'residence_visa_file_number',
        'residence_visa_issue_date',
        'residence_visa_expiry_date',
        'residence_visa_file',
        'emirates_id_number',
        'emirates_id_issue_date',
        'emirates_id_expiry_date',
        'emirates_id_file',
        'address',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'residence_visa_issue_date' => 'date',
        'residence_visa_expiry_date' => 'date',
        'emirates_id_issue_date' => 'date',
        'emirates_id_expiry_date' => 'date',
    ];

    /**
     * Get the relationships for the contact.
     */
    public function relationships()
    {
        return $this->hasMany(ContactRelationship::class, 'owner_id', 'contact_id')
            ->where('owner_type', '=', '3'); // Assuming type 3 is for contacts based on your data
    }

    /**
     * Get the reverse relationships for the contact.
     */
    public function reverseRelationships()
    {
        return $this->hasMany(ContactRelationship::class, 'owner_id_reverse', 'contact_id')
            ->where('owner_type_reverse', '=', '4'); // Assuming type 4 is for companies based on your data
    }

    /**
     * Get the companies that this contact is associated with.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_contact')
                    ->withPivot('position', 'department', 'role', 'start_date', 'end_date', 'is_primary', 'notes')
                    ->withTimestamps();
    }
}
