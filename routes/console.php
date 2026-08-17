<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel's scheduler only defines what should run - something external
// still has to invoke `php artisan schedule:run` every minute. On this
// WAMP/Windows box that means a Windows Task Scheduler entry (there's no
// cron); see the setup note left after this file's creation.
Schedule::command('analytics:close-stale-sessions')->everyFiveMinutes();
Schedule::command('analytics:aggregate')->dailyAt('00:15');
Schedule::command('analytics:cleanup')->weekly();
