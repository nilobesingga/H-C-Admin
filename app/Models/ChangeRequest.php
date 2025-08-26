<?php

namespace App\Models;

use App\Models\Bitrix\UserProfile;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    protected $table = 'change_request'; // Ensure the table name matches the migration
    protected $primaryKey = 'id'; // Optional, if you want to specify a custom primary key
    protected $fillable = [
        'model_type',
        'model_id',
        'field_name',
        'current_value',
        'proposed_value',
        'status',
        'reason',
        'requested_by',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(UserProfile::class,  'requested_by','user_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(UserProfile::class, 'reviewed_by', 'user_id');
    }

    public function changeable()
    {
        return $this->morphTo('model');
    }
}
