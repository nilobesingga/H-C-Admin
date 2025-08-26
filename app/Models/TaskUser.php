<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskUser extends Model
{
    protected $table = 'task_users';

    protected $fillable = [
        'task_id',
        'user_id',
        'type',
        'is_read',
        'read_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskModel::class, 'task_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('is_read', 'read_at')
                    ->withTimestamps();
    }
}
