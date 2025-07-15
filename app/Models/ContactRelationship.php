<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactRelationship extends Model
{
    protected $table = 'contact_relationship';
    protected $fillable = [
        'tec_custom_field_id',
        'owner_id',
        'owner_type',
        'owner',
        'name',
        'owner_reverse',
        'owner_id_reverse',
        'owner_type_reverse',
        'start_date',
        'end_date',
        'notes',
        'shares',
        'nominee_name',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the owner of the relationship.
     */
    public function owner()
    {
        if ($this->owner_type == '3') {
            return $this->belongsTo(Contact::class, 'owner_id', 'contact_id');
        }
        // Add other owner types as needed
        return null;
    }

    /**
     * Get the reverse owner of the relationship.
     */
    public function reverseOwner()
    {
        if ($this->owner_type_reverse == '4') {
            return $this->belongsTo(Contact::class, 'owner_id_reverse', 'contact_id');
        }
        // Add other owner types as needed
        return null;
    }
}
