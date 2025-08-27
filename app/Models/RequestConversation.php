<?php

namespace App\Models;

use Clue\Redis\Protocol\Model\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class RequestConversation extends Model
{
    protected $table = 'request_conversation';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];


    public function task(): BelongsTo
    {
        return $this->belongsTo(RequestModel::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RequestConversation::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(RequestConversation::class, 'parent_id')
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
        return $this->hasMany(RequestConversationFile::class, 'request_conversation_id');
    }
}
