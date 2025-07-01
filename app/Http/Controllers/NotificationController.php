<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class NotificationController extends Controller
{


    // Mark a notification as read
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }
    //count number of notifications using auth user
    public function countnotificationofuser()
    {
        $user = Auth::user();
        $notificationService = new NotificationService();
        $count = $notificationService->countnotificationofuser($user);
        return response()->json(['count' => $count]);
    }
    public function getallnotifications()
    {
        $user = Auth::user();
        //only data and id is needed, not the user 
        $notificationService = new NotificationService();
        $result = $notificationService->getallnotifications($user);
        if ($result->isEmpty()) {
            return response()->json(['message' => 'No notifications found'], 404);
        }
        return response()->json(['notifications' => $result]);
    }
}