<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    protected $table = 'company_documents';
    protected $fillable = [
        'company_id',
        'type',
        'year',
        'filename',
        'path',
        'url',
    ];

    /**
     * Get the company that owns the document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
