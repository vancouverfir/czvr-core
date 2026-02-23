<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected array $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            if (app()->bound('sentry')) {
                Integration::captureUnhandledException($e);
            }
        });
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render(Request $request, Throwable $exception): mixed
    {
        if ($exception instanceof CustomException) {
            return response()->view('errors.403', [], 403);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle unauthenticated users.
     */
    protected function unauthenticated(Request $request, AuthenticationException $exception): mixed
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest(route('index'));
    }
}
