<?php

namespace App\Services;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class NotificationService
{

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
            ->get(['id', 'data']);
        return $notifications;
    }
}