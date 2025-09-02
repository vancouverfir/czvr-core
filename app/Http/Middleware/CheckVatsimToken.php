<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckVatsimToken
{
    public function handle($request, Closure $next)
    {
        if (Session::has('vatsim_token_expires') && time() > Session::get('vatsim_token_expires')) {
            Auth::logout();
            Session::flush();
            return redirect('/')->with('error', 'Session expired, please log in again!');
        }

        return $next($request);
    }
}
