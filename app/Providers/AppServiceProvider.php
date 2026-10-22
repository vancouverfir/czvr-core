<?php

namespace App\Providers;

use App\Support\Authorization\PermissionCatalog;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        foreach (PermissionCatalog::slugs() as $permission) {
            Gate::define($permission, fn ($user): bool => $user->hasPermissionTo($permission));
        }

        Gate::define('settings.access', fn ($user): bool => $user->hasAnyPermission([
            PermissionCatalog::ROLES_MANAGE,
            PermissionCatalog::SITE_SETTINGS_MANAGE,
            PermissionCatalog::STAFF_MANAGE,
            PermissionCatalog::AUDIT_LOG_ACCESS,
            PermissionCatalog::NETWORK_MANAGE,
        ]));
        Gate::define('training.consolidation.override', fn ($user): bool => $user->canWaiveTrainingRequirements());
        Gate::define('training.stats', fn ($user): bool => $user->hasRole(SystemRoleCatalog::ADMINISTRATOR));
        Gate::define('instructing-sessions.access', fn ($user): bool => $user->hasAnyPermission([
            PermissionCatalog::INSTRUCTING_SESSIONS_MANAGE,
            PermissionCatalog::TRAINING_MANAGE,
        ]));

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('discord', Provider::class);
        });

        if ($this->app->environment('production')) {
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
