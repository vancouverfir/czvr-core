<?php

namespace App\Providers;

use App\Console\Commands\ActivityLog;
use App\Console\Commands\CacheVatsim;
use App\Console\Commands\CacheWeather;
use App\Console\Commands\CheckVisitHours;
use App\Console\Commands\CurrencyCheck;
use App\Console\Commands\RenewNotification;
use App\Console\Commands\SendSessionReminder;
use App\Console\Commands\SyncStudents;
use App\Console\Commands\FetchVatcanNotes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if ($this->app->environment('production')) {
            if (config('app.url')) {
                URL::forceRootUrl(config('app.url'));
            }
            URL::forceScheme('https');
        }

        // Schedule commands
        $this->app->booted(function (): void {
            $schedule = $this->app->make(Schedule::class);

            // * * * * * schedulers
            $schedule->command(ActivityLog::class)->everyMinute()->evenInMaintenanceMode()->sentryMonitor();
            $schedule->command(SendSessionReminder::class)->everyMinute();
            $schedule->command(CacheVatsim::class)->everyFiveMinutes();
            $schedule->command(CacheWeather::class)->everyFifteenMinutes();
            $schedule->command(RenewNotification::class)->hourly();
            $schedule->command(SyncStudents::class)->hourly();
            $schedule->command('vancouver:fetch-vatcan-notes')->hourlyAt(35);
            $schedule->command('backup:clean')->daily()->at('00:31');
            $schedule->command('backup:run')->daily()->at('01:01');
            // $schedule->command(EventReminders::class)->everyMinute();

            // 0 0 * * * schedulers
            // $schedule->command(RatingUpdate::class)->daily();

            // 0 0 1 * * schedulers
            $schedule->command(CheckVisitHours::class)->quarterly()->after(function (): void {
                Artisan::call(CurrencyCheck::class);
            });
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
