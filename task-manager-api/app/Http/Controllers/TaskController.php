<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCreeationRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Mail\TaskAssignedMail;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $page = $request->query('page', 1);

        $tasks = Task::with(['assignee', 'creator', 'assigner'])
            ->paginate($perPage, ['*'], 'page', $page);

        if ($tasks->count() === 0) {
            return response()->json(['message' => 'No tasks found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'tasks' => [
                'data' => TaskResource::collection($tasks)->resolve(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
                'next_page_url' => $tasks->nextPageUrl(),
                'prev_page_url' => $tasks->previousPageUrl(),
                'path' => $tasks->path(),
                'links' => $tasks->linkCollection(), // optional if you want links
            ]
        ], Response::HTTP_OK);
    }
    public function userTasks(Request $request)
    {
        $userId = Auth::id();
        $page = $request->query('page', 1);
        $tasks = Task::where('assigned_to', $userId)->paginate(1, ['*'], 'page', $page);;

        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'tasks' => $tasks
        ], Response::HTTP_OK);
    }
    public function show($id)
    {
        // Logic to retrieve a specific task by ID
        $task = Task::find($id);
        if ($task) {
            return response()->json(['message' => "Task retrieved successfully", 'task' => $task], Response::HTTP_OK);
        }
        return response()->json([
            'message' => "Task not found"
        ], Response::HTTP_NOT_FOUND);
    }
    public function store(TaskCreeationRequest $request)
    {
        $task = new Task($request->validated());
        $task->created_by = Auth::id();
        $task->assigned_by = Auth::id();
        $task->save();

        if ($task->assignee && $task->assignee->email) {
            Mail::to($task->assignee->email)->send(new TaskAssignedMail($task->id));
        }
        return response()->json(['message' => 'Task created successfully' . $task->assignee->name], Response::HTTP_CREATED);
    }
    public function update(TaskUpdateRequest $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
        $task->fill($request->validated());
        $task->updated_by = Auth::id();
        $task->save();
        return response()->json(['message' => "Task updated successfully"], Response::HTTP_OK);
    }
    public function updateTaskStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed',
        ]);

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        if (
            $validated['status'] === 'completed' &&
            (
                $task->status !== 'in_progress' ||
                Auth::id() !== $task->assigned_to
            )
        ) {
            return response()->json([
                'message' => 'Only the assigned user can mark an in-progress task as completed.'
            ], Response::HTTP_FORBIDDEN);
        }

        $task->status = $validated['status'];
        $task->completed_at = $validated['status'] === 'completed' ? now() : null;
        $task->updated_by = Auth::id();
        $task->save();

        return response()->json(['message' => 'Task status updated successfully'], Response::HTTP_OK);
    }
    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
            }
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'An error occurred while deleting the task',
                    'error' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function stats(Request $request)
    {
        $user = $request->user();


        // Admin sees all tasks; users only see their own
        if ($user->role->value === 'admin') {
            $total = Task::count();
            $pending = Task::where('status', 'pending')->count();
            $completed = Task::where('status', 'completed')->count();
        } else {
            $total = Task::where('assigned_to', $user->id)->count();
            $pending = Task::where('assigned_to', $user->id)->where('status', 'pending')->count();
            $completed = Task::where('assigned_to', $user->id)->where('status', 'completed')->count();
        }

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
        ]);
    }
}
