<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInstructor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (Auth::user()->permissions >= 3 || Auth::user()->instructorProfile !== null) {
                return $next($request);
            }
        }

        abort(403, 'Only Instructors have access to this resource!');
    }
}
