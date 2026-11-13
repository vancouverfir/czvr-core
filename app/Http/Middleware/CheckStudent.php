<?php

namespace App\Http\Middleware;

use App\Models\AtcTraining\Student;
use App\Support\Authorization\PermissionCatalog;
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
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('auth.connect.login');
        }

        if ($user->hasPermissionTo(PermissionCatalog::TRAINING_MANAGE)) {
            return $next($request);
        }

        $routeStudent = $request->route('student') ?? $request->route('id');
        $student = $routeStudent instanceof Student
            ? $routeStudent
            : ($routeStudent ? Student::find($routeStudent) : $user->studentProfile);

        if ($student && (int) $student->user_id === (int) $user->id) {
            return $next($request);
        }

        abort(403, 'You are not authorized to view this resource!');
    }
}
