<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class RequestModel extends Model
{
    use HasFactory;

    protected $table = 'request';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    /**
     * Get the company that owns the request.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * Get the contact that owns the request.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'contact_id');
    }

    /**
     * Get the user that created the request.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the request file associated with the request.
     */
    public function files()
    {
        return $this->hasMany(RequestFile::class, 'request_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RequestConversation::class, 'request_id')
                ->where('parent_id',0)
                ->with(['author','replies', 'files']);
    }
}
