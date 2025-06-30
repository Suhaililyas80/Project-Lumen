<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendTaskReminder extends Command
{
    protected $signature = 'task:send-reminder';
    protected $description = 'Send task reminders to users';

    public function handle()
    {
        $users = User::whereHas('tasks', function ($q) {
            $q->whereIn('status', ['pending', 'in-progress']);
        })->get();

        $period = 'daily'; // or 'weekly', etc.

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
            }
        }
        $this->info("Task daily reminders sent!");
    }
}