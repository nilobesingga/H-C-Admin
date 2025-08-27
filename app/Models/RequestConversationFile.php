<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestConversationFile extends Model
{
    protected $table = 'request_conversation_file';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    /**
     * Get the conversation that owns the file.
     */
    public function conversation()
    {
        return $this->belongsTo(RequestConversation::class, 'request_conversation_id');
    }

    /**
     * Get the parent file if this is a version of another file.
     */
    public function parent()
    {
        return $this->belongsTo(RequestConversationFile::class, 'parent_id');
    }

    /**
     * Get the file versions (children files).
     */
    public function versions()
    {
        return $this->hasMany(RequestConversationFile::class, 'parent_id');
    }
}
