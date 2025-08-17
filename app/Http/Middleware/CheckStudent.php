<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        if ($user->permissions >= 1 || $user->instructorProfile !== null) {
            return $next($request);
        }

        $studentId = $request->route('id') ?? $request->input('student_id');

        $student = \App\Models\AtcTraining\Student::find($studentId);
        if ($student && $student->user_id === $user->id) {
            return $next($request);
        }

        abort(403, 'You are not authorized to view this resource!');
    }
}
