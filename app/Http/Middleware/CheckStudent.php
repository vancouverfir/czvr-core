<?php

namespace App\Http\Middleware;

use App\Models\AtcTraining\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStudent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if (! $user) {
            session(['url.intended' => url()->full()]);

            return redirect()->route('auth.connect.login');
        }

        if ($user->permissions >= 1 || $user->instructorProfile !== null) {
            return $next($request);
        }

        $studentId = $request->route('id') ?? $request->input('student_id');

        $student = Student::find($studentId);
        if ($student && $student->user_id === $user->id) {
            return $next($request);
        }

        abort(403, 'You are not authorized to view this resource!');
    }
}
