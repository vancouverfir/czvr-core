<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticated
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            $request->session()->put('url.intended', $request->fullUrl());
            return redirect()->route('auth.connect.login');
        }

        return $next($request);
    }
}
