<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckExecutive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (Auth::user()->permissions >= 5) {
                return $next($request);
            }
        }

        abort(403, 'Only Administrators have access to this resource! IP Has been logged!');
    }
}
