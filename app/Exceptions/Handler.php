<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    protected array $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    protected function unauthenticated(Request $request, AuthenticationException $exception): mixed
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest(route('index'));
    }
}
