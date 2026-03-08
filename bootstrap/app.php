<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->authenticateSessions();
        $middleware->web(append: [
            \App\Http\Middleware\ForceMainDomain::class,
        ]);
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth_check' => \App\Http\Middleware\Authenticated::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'booking_certified' => \App\Http\Middleware\BookingIsCertified::class,
            'executive' => \App\Http\Middleware\CheckExecutive::class,
            'staff' => \App\Http\Middleware\CheckStaff::class,
            'instructor' => \App\Http\Middleware\CheckInstructor::class,
            'student' => \App\Http\Middleware\CheckStudent::class,
            'certified' => \App\Http\Middleware\CheckCertified::class,
            'notcertified' => \App\Http\Middleware\CheckNotCertified::class,
            'privacy' => \App\Http\Middleware\CheckIfPrivacy::class,
            'atc' => \App\Http\Middleware\CheckAtc::class,
            'mentor' => \App\Http\Middleware\CheckMentor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (! $request->expectsJson()) {
                return redirect()->guest(route('auth.connect.login'));
            }
        });
    })
    ->create();
