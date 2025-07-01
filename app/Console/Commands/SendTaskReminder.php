<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTaskReminder extends Command
{
    protected $signature = 'task:send-reminder';
    protected $description = 'Send task reminders to users';

    public function handle()
    {
        Log::info("Starting task reminder email sending process...");
        $users = User::whereHas('tasks', function ($q) {
            $q->whereIn('status', ['pending', 'in-progress']);
        })->get();

        $period = 'daily'; // or 'weekly', etc.
        var_dump("hereeee");
        foreach ($users as $user) {
            $tasks = $user->tasks()->whereIn('status', ['pending', 'in-progress'])->get();
            if ($tasks->count() > 0) {
                Mail::send('emails.task_reminder', [
                    'user' => $user,
                    'tasks' => $tasks,
                    'period' => $period,
                ], function ($message) use ($user, $period) {
                    $message->to($user->email, $user->name)
                        ->subject("Your {$period} task reminder");
                });
                Log::info("Sending task reminder email to {$user->email}");
            }
        }
        $this->info("Task daily reminders sent!");
    }
}