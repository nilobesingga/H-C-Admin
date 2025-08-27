<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskConversationFile extends Model
{
    use HasFactory;

    protected $table = 'task_conversation_file';

    protected $fillable = [
        'task_conversation_id',
        'file_name',
        'file_path',
        'parent_id'
    ];

    /**
     * Get the conversation that owns the file.
     */
    public function conversation()
    {
        return $this->belongsTo(TaskConversation::class, 'task_conversation_id');
    }

    /**
     * Get the parent file if this is a version of another file.
     */
    public function parent()
    {
        return $this->belongsTo(TaskConversationFile::class, 'parent_id');
    }

    /**
     * Get the file versions (children files).
     */
    public function versions()
    {
        return $this->hasMany(TaskConversationFile::class, 'parent_id');
    }
}
