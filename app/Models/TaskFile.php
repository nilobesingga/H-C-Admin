<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskFile extends Model
{
    protected $table = 'task_file';

    protected $fillable = [
        'task_id',
        'parent_id',
        'file_name',
        'file_path'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskModel::class, 'task_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskFile::class, 'parent_id');
    }
}
