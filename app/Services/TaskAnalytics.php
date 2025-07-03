<?php

namespace App\Services;
use App\Models\User;
use App\Models\TaskManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskAnalytics
{
    public function getNumberOfTasksByStatus()
    {
        $user = Auth::user();
        $statuses = ['pending', 'in_progress', 'completed'];
        $result = [];
        if (!$user->roles->contains('role', 'admin')) {
            // Per-user stats
            $taskCounts = TaskManagement::select('status', DB::raw('count(*) as total'))
                ->where('user_id', $user->id)
                ->groupBy('status')
                ->pluck('total', 'status');

            // Map counts by status
            foreach ($statuses as $status) {
                $result[$status] = $taskCounts[$status] ?? 0;
            }
            $result['total'] = TaskManagement::where('user_id', $user->id)->count();
            $result['deleted'] = $user->tasks()->onlyTrashed()->count();
            return $result;
        } else {
            // Admin: all tasks
            $taskCounts = TaskManagement::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            foreach ($statuses as $status) {
                $result[$status] = $taskCounts[$status] ?? 0;
            }
            $result['total'] = TaskManagement::withTrashed()->count();
            $result['deleted'] = TaskManagement::onlyTrashed()->count();
            return $result;
        }
    }
    public function getTasksDueToday($user)
    {

        $today = Carbon::now()->toDateString();
        // need count of tasks due today
        $taskduetoday = TaskManagement::whereDate('end_date', $today)
            ->where('status', '!=', 'completed'); // Exclude completed tasks
        // check if user is admin or not
        if (!$user->roles->contains('role', 'admin')) {
            // If not admin, filter tasks by user_id
            $taskduetoday = $taskduetoday->where('user_id', $user->id);
        }
        $taskduetoday = $taskduetoday->get();
        $taskCount = $taskduetoday->count();
        return [
            'count' => $taskCount,
            'tasks' => $taskduetoday
        ];
    }
}
