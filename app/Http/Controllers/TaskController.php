<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\TaskCompany;
use App\Models\TaskConversation;
use App\Models\TaskConversationFile;
use App\Models\TaskModel;
use App\Models\TaskFile;
use App\Models\TaskUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function getList(Request $request)
    {
        $perPage = $request->perPage ?? 10;
        $query = TaskModel::where('user_id', Auth::id())
                ->with([
                    'users',
                    'files',
                    'creator',
                    'comments',
                    'companies',
                    'participants',
                    'observers'
                ])
                ->where('status', $request->status)
                ->orderBy('created_at', 'desc')
                ->orderBy('is_priority', 'desc');

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Check if limit parameter is set (for backwards compatibility)
        if ($request->has('limit') && !empty($request->limit)) {
            return response()->json($query->limit($request->limit)->get());
        }

        // Return paginated results
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
            'responsible' => 'required|array',
            'responsible.*.id' => 'required|exists:users,id',
            'participants' => 'nullable|array',
            'participants.*.id' => 'exists:users,id',
            'observers' => 'nullable|array',
            'observers.*.id' => 'exists:users,id',
            'companies.*.id' => 'exists:companies,company_id',
        ]);

        // Create task
        $task = TaskModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'user_id' => Auth::id(),
            'status' => 'open', // Default status
            'is_priority' => ($request->is_priority === true) ? 1 : 0, // Default to false if not provided
            'category' => $request->category ?? 'individual', // Default to individual if not provided
        ]);

        // Store files if any
        if ($request->has('companies')) {
            foreach ($request->companies as $company) {
                TaskCompany::create([
                    'task_id' => $task->id,
                    'company_id' => $company['id'],
                ]);
            }
        }

        // Store files if any
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('task-files/' . $task->id, 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
        }



        // Add responsible persons
        foreach ($request->responsible as $user) {
            TaskUser::create([
                'task_id' => $task->id,
                'user_id' => $user['id'],
                'type' => 'responsible',
                'is_read' => 0
            ]);
        }

        // Add participants if any
        if ($request->has('participants')) {
            foreach ($request->participants as $user) {
                TaskUser::create([
                    'task_id' => $task->id,
                    'user_id' => $user['id'],
                    'type' => 'participant'
                ]);
            }
        }

        // Add observers if any
        if ($request->has('observers')) {
            foreach ($request->observers as $user) {
                TaskUser::create([
                    'task_id' => $task->id,
                    'user_id' => $user['id'],
                    'type' => 'observer'
                ]);
            }
        }
        event(new \App\Events\TaskCreated($task));
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load('files', 'users')
        ], 201);
    }

    public function storeComment(Request $request, $taskId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $task = TaskModel::findOrFail($taskId);

        if($request->has('status')) {
            $task->status = $request->status;
            $task->save();
        }

        $comment = $task->comments()->create([
            'message' => $request->message,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id ?? 0
        ]);

         // Store files if any
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('task-comment-files/' . $comment->id, 'public');

                TaskConversationFile::create([
                    'task_conversation_id' => $comment->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => $comment->load('author')
        ], 201);
    }

    public function getTaskDetails($taskId)
    {
        $task = TaskModel::with([
            'users',
            'files',
            'creator',
            'comments',
            'companies',
            'participants',
            'observers',
            'responsible'
        ])->findOrFail($taskId);

        return response()->json($task);
    }
}
