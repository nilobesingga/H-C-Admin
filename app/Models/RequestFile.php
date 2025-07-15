<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestFile extends Model
{
    use HasFactory;

    protected $table = 'request_file';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    /**
     * Get the company that owns the request file.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the contact that owns the request file.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user that created the request file.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the requests associated with this file.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(RequestModel::class, 'id');
    }
}
