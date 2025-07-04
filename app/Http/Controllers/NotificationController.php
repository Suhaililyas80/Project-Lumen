<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotificationController extends Controller
{


    // Mark a notification as read
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notificationService = new NotificationService();
        return $notificationService->markAsRead($user, $notificationId);
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