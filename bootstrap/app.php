<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authenticated;
use App\Http\Middleware\BookingIsCertified;
use App\Http\Middleware\CheckAtc;
use App\Http\Middleware\CheckCertified;
use App\Http\Middleware\CheckExecutive;
use App\Http\Middleware\CheckIfPrivacy;
use App\Http\Middleware\CheckInstructor;
use App\Http\Middleware\CheckMentor;
use App\Http\Middleware\CheckNotCertified;
use App\Http\Middleware\CheckStaff;
use App\Http\Middleware\CheckStudent;
use App\Http\Middleware\ForceMainDomain;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AppServiceProvider::class,
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
            ForceMainDomain::class,
        ]);
        $middleware->group('api', [
            'throttle:api',
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'auth_check' => Authenticated::class,
            'guest' => RedirectIfAuthenticated::class,
            'booking_certified' => BookingIsCertified::class,
            'executive' => CheckExecutive::class,
            'staff' => CheckStaff::class,
            'instructor' => CheckInstructor::class,
            'student' => CheckStudent::class,
            'certified' => CheckCertified::class,
            'notcertified' => CheckNotCertified::class,
            'privacy' => CheckIfPrivacy::class,
            'atc' => CheckAtc::class,
            'mentor' => CheckMentor::class,
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
