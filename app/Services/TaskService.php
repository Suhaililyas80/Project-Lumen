<?php
namespace App\Services;
use App\Models\User;
use App\Models\TaskManagement;
use App\Models\IdRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskService
{
    public function createTask(array $data)
    {
        $user = Auth::user();
        // i have role_id in users table, role in id_roles table
        if (!$user->roles->contains('role', 'admin')) {
            throw new \Exception('Unauthorized action, you do not have permission to create a task.', 403);
        }
        $data['assigned_by'] = $user->id;
        $data['start_date'] = Carbon::now();
        $data['status'] = 'pending'; // Default status, can be changed later

        try {
            $task = TaskManagement::create($data);
            return [
                'data' => $task,
                'status' => 201,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create task: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function updateTask(int $id, array $data)
    {
        $user = Auth::user();
        // Check if the user has permission to update the task
        $task = TaskManagement::find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }
        if ($task->assigned_by !== $user->id) {
            throw new \Exception('Unauthorized action, you do not have permission to update this task.', 403);
        }
        try {
            $task->update($data);
            return [
                'data' => $task,
                'status' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update task: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function updateTaskStatus(int $id, string $status)
    {
        $user = Auth::user();
        // Check if the user has permission to update the task status
        $task = TaskManagement::find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }
        //user cannot delete task, only assigneby can delete task
        if ($status === 'deleted') {
            if ($task->assigned_by !== $user->id) {
                throw new \Exception('Unauthorized action, you do not have permission to delete this task.', 403);
            }
            $task->delete();
            return [
                'success' => true,
                'message' => 'Task deleted successfully',
                'status' => 200,
            ];
        }
        if ($task->user_id !== $user->id) {
            throw new \Exception('Unauthorized action, you do not have permission to update the status of this task.', 403);
        }
        try {
            $task->status = $status;
            $task->save();
            return [
                'success' => true,
                'message' => 'Task status updated successfully',
                'data' => $task,
                'status' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update task status: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function getTasks(array $filters = [], $page = 1, $perPage = 10)
    {

        // one check if auth user is admin, then only he can see all tasks,if he is not admin, then he can see only his tasks
        $user = Auth::user();
        if (!$user->roles->contains('role', 'admin')) {
            $filters['user_id'] = $user->id;
        }
        $query = TaskManagement::query();
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['assigned_by'])) {
            $query->where('assigned_by', $filters['assigned_by']);
        }
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        $tasks = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $tasks,
            'message' => 'Tasks retrieved successfully',
            'status' => 200,
        ];
    }

    public function getTaskDetail(int $id)
    {
        $task = TaskManagement::find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }
        return [
            'data' => $task,
            'message' => 'Task details retrieved successfully',
            'status' => 200,
        ];
    }
}
// service level cleanup
// sorting backend
//task detail by taskid