<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAtc
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (Auth::user()->permissions >= 1) {
                return $next($request);
            }
        }

        abort(403, 'Only Current Controllers have access to this resource!');
    }
}
