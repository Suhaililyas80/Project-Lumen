<?php

namespace App\Http\Controllers;

use App\Models\IdRole;
use App\Services\TaskService;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
class TaskManagementController extends Controller
{
    // Method to create a new task
    // app/Http/Controllers/TaskManagementController.php

    public function createTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'end_date' => 'nullable|date', // match model's field, if you want 'due_date', update model too
            //'status'     => 'required|in:pending,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }
        $validData = $validator->validated();
        $taskService = new TaskService();
        $result = $taskService->createTask($validData);
        return response()->json($result, $result['status']);
    }

    // admin can update task title, description, end_date of task created by him
    public function updateTask(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'end_date' => 'sometimes|nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }
        $validData = $validator->validated();
        $taskService = new TaskService();
        $result = $taskService->updateTask($taskId, $validData);
        return response()->json($result, $result['status']);
    }

    // user update task status
    public function updateTaskStatus(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,deleted',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }
        $validData = $validator->validated()['status'];

        $taskService = new TaskService();
        $result = $taskService->updateTaskStatus($taskId, $validData);
        return response()->json($result, $result['status']);
    }

    //get all tasks and filtering by user_id, status, assigned_by,title
    public function getTasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed,deleted',
            'assigned_by' => 'nullable|exists:users,id',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }
        $filters = [];
        $filters['user_id'] = $request->input('user_id');
        $filters['status'] = $request->input('status');
        $filters['assigned_by'] = $request->input('assigned_by');
        $filters['title'] = $request->input('title');
        //for pagination
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $taskService = new TaskService();
        $result = $taskService->getTasks($filters, $page, $perPage);
        return response()->json($result, $result['status']);
    }
    //task detail bye taskid

    public function getTaskDetail(Request $request, $taskId)
    {
        $taskService = new TaskService();
        $result = $taskService->getTaskDetail($taskId);
        return response()->json($result, 200);
    }




}
