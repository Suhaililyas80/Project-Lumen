<?php
namespace App\Services;
use App\Models\User;
use App\Models\TaskManagement;
use App\Models\IdRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\TaskCreated;
use App\Models\Notification;
use Illuminate\Support\Str;

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
        //end_date must be greater than start_date and equal to start_date
        if (
            isset($data['end_date'], $data['start_date']) &&
            Carbon::parse($data['end_date'])->toDateString() < Carbon::parse($data['start_date'])->toDateString()
        ) {
            throw new \Exception('End date must be greater than or equal to the start date.', 422);
        }

        $data['status'] = 'pending'; // Default status, can be changed later

        try {
            $task = TaskManagement::create($data);
            // Notify the user about the task creation
            $this->notifyUserAboutTask($task->id, 'assigned');
            // notify the user about the task creation
            //store the task creation event in table
            Notification::create([
                'type' => 'task_created',
                'user_id' => $task->user_id,
                'data' => json_encode([
                    'title' => $task->title,
                    'description' => $task->description,
                    'end_date' => $task->end_date,
                ]),
            ]);
            // Fire the event to notify the user
            // This will broadcast the event to the user
            event(new TaskCreated($task->user_id, $task->title, $task->description, $task->end_date));
            return [
                'success' => true,
                'message' => 'Task created successfully',
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
            // Notify the user about the task update
            $this->notifyUserAboutTask($task->id, 'updated');
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

    //notify user about task creation and update done by admin

    public function notifyUserAboutTask(int $id, $type = 'assigned')
    {
        $task = TaskManagement::find($id);
        if (!$task) {
            throw new \Exception('Task not found', 404);
        }

        $user = User::find($task->user_id);
        if ($user && $user->email) {
            $data = [
                'user' => $user,
                'task' => $task,
                'type' => $type, // 'assigned' or 'updated'
            ];
            \Mail::send('emails.task_notification', $data, function ($message) use ($user, $type) {
                $subject = $type === 'assigned'
                    ? 'A new task has been assigned to you'
                    : 'A task assigned to you has been updated';
                $message->to($user->email, $user->name)
                    ->subject($subject);
            });

            return [
                'success' => true,
                'message' => 'Notification sent',
                'status' => 200,
            ];
        } else {
            throw new \Exception('User email not found', 404);
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
            // Soft delete the task
            $task->deleted_at = Carbon::now();
            $task->save();
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

        $tasks = $query->limit($perPage)->offset(($page - 1) * $perPage)->get();

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
