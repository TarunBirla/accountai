<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduler - AccountAI
|--------------------------------------------------------------------------
*/

// Run daily reminder email check at 08:00 AM every business day / morning
Schedule::command('reminders:send-due')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders-scheduler.log'));
