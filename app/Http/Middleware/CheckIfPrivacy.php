<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfPrivacy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (Auth::user()->init == 0) {
                return $next($request);
            }
        }

        return '/'->with('error', 'Please accept the CZVR privacy policy!');
    }
}
