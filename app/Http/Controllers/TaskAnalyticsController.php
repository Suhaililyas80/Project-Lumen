<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskAnalytics; // Assuming you have a service for task analytics
use App\Models\TaskManagement;
use Carbon\Carbon;

class TaskAnalyticsController extends Controller
{
    public function getNumberOfTasksByStatus(Request $request)
    {
        //call thr taskAnalytics service
        $taskAnalyticsService = new TaskAnalytics();
        try {
            $taskCounts = $taskAnalyticsService->getNumberOfTasksByStatus();
            return response()->json([
                'success' => true,
                'data' => $taskCounts,
                'message' => 'Task counts retrieved successfully',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve task counts: ' . $e->getMessage(),
                'status' => 500,
            ]);
        }
    }

    //get all tasks which have todat's date as end_date
    public function getTasksDueToday(Request $request)
    {
        $today = Carbon::now()->toDateString(); // Get today's date in 'Y-m-d' format
        // need count of tasks due today
        $taskduetoday = TaskManagement::whereDate('end_date', $today)
            ->where('status', '!=', 'completed') // Exclude completed tasks
            ->get();
        $taskCount = $taskduetoday->count();
        return response()->json([
            'success' => true,
            'data' => [
                'count' => $taskCount,
                'tasks' => $taskduetoday,
            ],
            'message' => 'Tasks due today retrieved successfully',
            'status' => 200,
        ]);
    }
}