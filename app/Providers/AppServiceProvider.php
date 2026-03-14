<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('discord', Provider::class);
        });

        if ($this->app->environment('production')) {
            if (config('app.url')) {
                URL::forceRootUrl(config('app.url'));
            }
            URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
