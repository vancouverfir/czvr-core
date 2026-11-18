<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->deleted) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'This account has been deleted.'], 401)
                : redirect()->route('index');
        }

        if ($user && ! $user->init && ! $request->routeIs('index', 'privacy', 'privacy.accept', 'privacy.deny', 'auth.logout')) {
            return $request->expectsJson()
                ? response()->json(['message' => 'You must accept the privacy policy first.'], 403)
                : redirect()->route('index');
        }

        return $next($request);
    }
}
