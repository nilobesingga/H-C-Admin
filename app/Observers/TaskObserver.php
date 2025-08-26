<?php

namespace App\Observers;

use App\Events\TaskCreated;
use App\Models\TaskModel;

class TaskObserver
{
    /**
     * Handle the TaskModel "created" event.
     */
    public function created(TaskModel $task): void
    {
        broadcast(new TaskCreated($task))->toOthers();
    }
}
