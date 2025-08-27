<?php

namespace App\Models;

use App\Models\Bitrix\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskConversation extends Model
{
    protected $table = 'task_conversation';

    protected $fillable = [
        'task_id',
        'message',
        'user_id',
        'parent_id'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskModel::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskConversation::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TaskConversation::class, 'parent_id')
                    ->with(['author','replies','files']);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')
            ->leftJoin('user_profile', 'user_profile.user_id', '=', 'users.id')
            ->select('users.*', 'user_profile.name as author_name');

    }
    public function files(): HasMany
    {
        return $this->hasMany(TaskConversationFile::class, 'task_conversation_id');
    }
}
