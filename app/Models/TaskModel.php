<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use function Laravel\Prompts\select;

class TaskModel extends Model
{
    protected $table = 'task';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'description',
        'deadline',
        'user_id',
        'status',
        'is_priority',
        'category',
    ];

    protected $casts = [
        'deadline' => 'date',
        'is_priority' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskConversation::class, 'task_id')
                ->with(['author','replies', 'files']);
    }

    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class, 'task_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')
                    ->select('users.*','task_users.type')
                    ->with('userprofile')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    /**
     * Get the companies associated with the task.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'task_company', 'task_id', 'company_id')
                    ->select('companies.*')
                    ->withTimestamps();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')
                    ->select('users.*','task_users.type')
                    ->with('userprofile')
                    ->withPivot('type')
                    ->where('task_users.type', 'participant')
                    ->withTimestamps();
    }

    public function observers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')
                    ->select('users.*','task_users.type')
                    ->with('userprofile')
                    ->withPivot('type')
                    ->where('task_users.type', 'observer')
                    ->withTimestamps();
    }

    public function responsible(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')
                    ->select('users.*','task_users.type')
                    ->with('userprofile')
                    ->withPivot('type')
                    ->where('task_users.type', 'responsible')
                    ->withTimestamps();
    }
}
