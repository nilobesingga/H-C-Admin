<?php

namespace App\Console\Commands;

use App\Events\TaskCreated;
use App\Models\TaskModel;
use Illuminate\Console\Command;

class TestBroadcast extends Command
{
    protected $signature = 'test:broadcast';
    protected $description = 'Create a test task to verify broadcasting';

    public function handle()
    {
        $task = TaskModel::create([
            'title' => 'Test Broadcast Task',
            'description' => 'This is a test task to verify broadcasting',
            'status' => 'open',
            'deadline' => now()->addDays(7),
            'user_id' => 196,
            'category' => 'individual',
            'is_priority' => false,
        ]);

        // Event will be triggered via observer, but let's also broadcast it directly to verify
        broadcast(new TaskCreated($task))->toOthers();

        $this->info('Test task created with ID: ' . $task->id);
        $this->info('Event broadcasted to tasks channel');
    }
}
