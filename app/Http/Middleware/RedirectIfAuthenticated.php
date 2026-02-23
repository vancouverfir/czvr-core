<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): mixed
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        if (Auth::check() && Auth::user()->init == 0) {
            return redirect('/')->with('danger', 'Please accept the privacy policy!');
        }

        return $next($request);
    }
}
