<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// // Runs every weekday at 18:00 (6 PM)
Schedule::command('attendance:mark-absent')->weekdays()->dailyAt('18:00');
// then run 'php artisan schedule:work' to run the scheduler loop locally (keeps running, checks every minute)

// // for testing, then run 'php artisan schedule:run'
// Schedule::command('attendance:mark-absent')
//     ->everyMinute();

// // or run 'php artisan attendance:mark-absent' instead to execute the command directly which bypasses the scheduler, thus ignoring dailyAt('18:00') or ->everyMinute()