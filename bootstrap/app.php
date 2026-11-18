<?php

use App\Http\Middleware\CheckCertified;
use App\Http\Middleware\CheckStudent;
use App\Http\Middleware\ForceMainDomain;
use App\Http\Middleware\EnsureAccountIsActive;
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
        $middleware->trustHosts(at: fn () => array_map(
            fn (string $host): string => '^'.preg_quote($host, '/').'$',
            [config('app.domain'), config('app.booking_domain'), config('app.training_domain')],
        ), subdomains: false);
        $middleware->validateCsrfTokens(except: ['emails/unsubscribe/*']);
        $middleware->authenticateSessions();
        $middleware->web(append: [
            ForceMainDomain::class,
            EnsureAccountIsActive::class,
        ]);
        $middleware->throttleApi();
        $middleware->redirectGuestsTo(fn (Request $request): string => route('auth.connect.login'));
        $middleware->redirectUsersTo(fn (Request $request): string => route('index'));

        $middleware->alias([
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
