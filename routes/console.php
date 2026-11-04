<?php

use App\Models\Habit;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminders and housekeeping. Production needs one cron line: * * * * * php artisan schedule:run
Schedule::command('habitude:send-reminders')->everyMinute()->withoutOverlapping();
Schedule::command('model:prune', ['--model' => [Habit::class]])->daily();
