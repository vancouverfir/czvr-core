<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::check()) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('auth.connect.login');
        }

        return $next($request);
    }
}
