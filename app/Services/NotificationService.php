<?php

namespace App\Services;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class NotificationService
{
    public function markAsRead($user, $notificationId)
    {
        $notification = $user->notifications()->find($notificationId);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        $notification->read_at = Carbon::now();
        $notification->save(); // Pass the notification instance as required by the overridden save() method\
        return response()->json(['message' => 'Notification marked as read']);
    }
    public function countnotificationofuser($user)
    {
        // count only unread notifications for the authenticated user
        $count = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
        return $count;
    }
    public function getallnotifications($user)
    {
        // get the user all data and notification ids ordered by created_at
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'data', 'read_at']);
        return $notifications;
    }
}