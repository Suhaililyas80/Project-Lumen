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
        // Schedule the task reminder command to run daily at 10 PM
        $schedule->command('task:send-reminder')->dailyAt('22:00');
    }
}
