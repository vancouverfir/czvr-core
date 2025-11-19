<?php

namespace App\Console;

use App\Console\Commands\ActivityLog;
use App\Console\Commands\CheckVisitHours;
use App\Console\Commands\CurrencyCheck;
use App\Console\Commands\EventReminders;
use App\Console\Commands\RatingUpdate;
use App\Console\Commands\RenewNotification;
use App\Console\Commands\SendSessionReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // * * * * * schedulers
        $schedule->command(ActivityLog::class)->everyMinute()->evenInMaintenanceMode()->sentryMonitor();
        $schedule->command(SendSessionReminder::class)->everyMinute();
        $schedule->command('vancouver:cachevatsim')->everyFiveMinutes();
        $schedule->command('vancouver:cacheweather')->everyFifteenMinutes();
        $schedule->command(RenewNotification::class)->hourly();
        $schedule->command('backup:clean')->daily()->at('00:31');
        $schedule->command('backup:run')->daily()->at('01:01');
        // $schedule->command(EventReminders::class)->everyMinute();

        // 0 0 * * * schedulers
        // $schedule->command(RatingUpdate::class)->daily();

        // 0 0 1 * * schedulers
        $schedule->command(CheckVisitHours::class)->quarterly()->after(function () {
            Artisan::call(CurrencyCheck::class);
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
