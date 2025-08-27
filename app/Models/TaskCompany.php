<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompany extends Model
{
    protected $table = 'task_company';

    protected $fillable = [
        'task_id',
        'company_id'
    ];

    /**
     * Get the task that owns the task company relation.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskModel::class, 'task_id');
    }

    /**
     * Get the company that owns the task company relation.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
