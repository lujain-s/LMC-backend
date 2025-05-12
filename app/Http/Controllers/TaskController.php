<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    //the first correct version
    /*public function assignTask(Request $request)
    {
        $validated = $request->validate([
            'Description' => 'required|string',
            'Deadline' => 'required|date',
            'role_id' => 'nullable|integer',
            'user_id' => 'nullable|integer'
        ]);

        $creatorId = auth()->id();
        if (!$creatorId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!empty($validated['user_id']) && $validated['user_id'] == $creatorId) {
            return response()->json(['message' => 'You cannot assign a task to yourself.'], 400);
        }

        $result = $this->taskService->assignTask($creatorId, $validated);

        return response()->json($result['data'], $result['status']);
    }*/

    public function assignTask(Request $request)
    {
     $validated = $request->validate([
        'Description' => 'required|string',
        'Deadline' => 'required|date',
        'role_id' => 'nullable|integer',
        'user_id' => 'nullable|integer',
        'RequiresInvoice' => 'boolean',
     ]);

     $creatorId = auth()->id();
     if (!$creatorId) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
     }

     if (!empty($validated['user_id']) && $validated['user_id'] == $creatorId) {
        return response()->json(['message' => 'You cannot assign a task to yourself.'], 400);
     }

     $result = $this->taskService->assignTask($creatorId, $validated);

     return response()->json($result['data'], $result['status']);
    }

    public function completeUserTask($taskId): JsonResponse
    {
        try {
            $result = $this->taskService->completeUserTask($taskId, auth()->id());
            return response()->json([
                'message' => 'Task marked as completed',
                'your_status' => true,
                'task_status' => $result['task_status'],
                'completion_time' => $result['completion_time']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'debug_info' => $e->getCode() === 404 ? [
                    'user_id' => auth()->id(),
                    'task_id' => $taskId
                ] : null
            ], $e->getCode() ?: 500);
        }
    }

    public function showTasks(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'status' => 'nullable|in:Pending,Done',
            'user_id' => 'nullable|integer|exists:users,id',
            'task_id' => 'nullable|integer|exists:tasks,id'
        ]);

        try {
            $result = $this->taskService->getTasks($validatedData);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    public function myTasks(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'status' => 'nullable|in:Pending,Done',
            'task_id' => 'nullable|integer|exists:tasks,id'
        ]);

        try {
            $result = $this->taskService->getUserTasks($validatedData, auth()->id());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    public function updateTaskStatus (Request $request) {

    }
}
