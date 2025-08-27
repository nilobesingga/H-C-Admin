<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetup extends Model
{
    protected $table = 'company_setup';
    protected $fillable = [
        'description',
        'timeframe',
        'language',
        'contact_method',
        'contact_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id',   'contact_id');
    }
}
