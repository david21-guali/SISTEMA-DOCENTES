<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Reminders
use Illuminate\Support\Facades\Schedule;

// Send meeting reminders every hour (checks 24h ahead)
Schedule::command('meetings:send-reminders')->hourly();

// Send task deadline reminders daily at 8 AM (checks 2 days ahead)
Schedule::command('tasks:send-reminders')->dailyAt('08:00');

// Send project deadline reminders daily at 9 AM (checks 7 days ahead)
Schedule::command('projects:send-reminders')->dailyAt('09:00');
