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
        if (!$user) {
            throw new \Exception('Unauthorized action, you do not have permission to view task analytics.', 403);
        }

        $statuses = ['pending', 'in_progress', 'completed'];
        $result = [];

        if (!$user->roles->contains('role', 'admin')) {
            // Per-user stats
            $taskCounts = TaskManagement::select('status', DB::raw('count(*) as total'))
                ->where('user_id', $user->id)
                ->groupBy('status')
                ->get();

            // Map counts by status
            foreach ($statuses as $status) {
                $count = $taskCounts->firstWhere('status', $status);
                $result[$status] = $count ? $count->total : 0;
            }

            $result['total'] = TaskManagement::where('user_id', $user->id)->count();
            $result['deleted'] = $user->tasks()->onlyTrashed()->count();
            return $result;
        } else {
            // Admin: all tasks
            $taskCounts = TaskManagement::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
            foreach ($statuses as $status) {
                $count = $taskCounts->firstWhere('status', $status);
                $result[$status] = $count ? $count->total : 0;
            }
            $result['total'] = TaskManagement::withTrashed()->count();
            $result['deleted'] = TaskManagement::onlyTrashed()->count();
            return $result;
        }
    }
}
