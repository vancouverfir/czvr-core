<?php

use App\Console\Commands\ActivityLog;
use App\Console\Commands\CacheVatsim;
use App\Console\Commands\CacheWeather;
use App\Console\Commands\CheckVisitHours;
use App\Console\Commands\CurrencyCheck;
use App\Console\Commands\FetchVatcanNotes;
use App\Console\Commands\RenewNotification;
use App\Console\Commands\SendSessionReminder;
use App\Console\Commands\SyncStudents;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command(ActivityLog::class)->everyMinute()->evenInMaintenanceMode()->sentryMonitor();
Schedule::command(SendSessionReminder::class)->everyMinute();
Schedule::command(CacheVatsim::class)->everyMinute();
Schedule::command(CacheWeather::class)->everyFifteenMinutes();
Schedule::command(FetchVatcanNotes::class)->everyFifteenMinutes()->withoutOverlapping(15);
Schedule::command(RenewNotification::class)->hourly()->withoutOverlapping(15);
Schedule::command(SyncStudents::class)->hourly()->withoutOverlapping(30);
Schedule::command('backup:clean')->daily()->at('00:31');
Schedule::command('backup:run')->daily()->at('01:01');
// Schedule::command(EventReminders::class)->everyMinute();

Schedule::command(CheckVisitHours::class)->quarterly()->after(function (): void {
    Artisan::call(CurrencyCheck::class);
});
