<?php

use App\Http\Middleware\BookingIsCertified;
use App\Http\Middleware\CheckCertified;
use App\Http\Middleware\CheckStudent;
use App\Http\Middleware\ForceMainDomain;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustHosts();
        $middleware->validateCsrfTokens(except: ['emails/unsubscribe/*']);
        $middleware->authenticateSessions();
        $middleware->web(append: [
            ForceMainDomain::class,
        ]);
        $middleware->throttleApi();
        $middleware->redirectGuestsTo(fn (Request $request): string => route('auth.connect.login'));
        $middleware->redirectUsersTo(fn (Request $request): string => route('index'));

        $middleware->alias([
            'booking_certified' => BookingIsCertified::class,
            'student' => CheckStudent::class,
            'certified' => CheckCertified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
