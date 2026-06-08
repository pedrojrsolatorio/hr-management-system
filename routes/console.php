<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// // Runs every weekday at 18:00 (6 PM)
// Schedule::command('attendance:mark-absent')->weekdays()->dailyAt('18:00');

// // for testing, then run 'php artisan schedule:run'
Schedule::command('attendance:mark-absent')
    ->everyMinute();
// // or run 'php artisan attendance:mark-absent' to execute the command directly which bypassing the scheduler, thus ignoring dailyAt('18:00') or ->everyMinute()