<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNotCertified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (Auth::user()->permissions == 0) {
                return $next($request);
            }
        }

        abort(403, 'You are already a certified Vancouver controller. (NOTCERTIFIED check)');
    }
}
