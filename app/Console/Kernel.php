<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendTaskReminder::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command("task:send-reminder")->everyMinute();
    }
}