<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AtcTraining\Roster;

class BookingIsCertified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()
                ->withErrors(['certification' => 'You must be logged in to book a position!'])
                ->withInput();
        }

        $callsign = $request->input('callsign');

        if (!$callsign) {
            return redirect()->back()
                ->withErrors(['certification' => 'Invalid or missing callsign!'])
                ->withInput();
        }

        $roster = Roster::where('cid', $user->id)->first();

        if (!$roster) {
            return redirect()->back()
                ->withErrors(['certification' => 'You are not on the roster!'])
                ->withInput();
        }

        if (!$this->userHasCertification($roster, $callsign)) {
            return redirect()->back()
                ->withErrors(['certification' => 'Nice Try! 🤣🤣🤣'])
                ->withInput();
        }

        return $next($request);
    }

    protected function userHasCertification(Roster $roster, string $callsign): bool
    {
        [$icao, $position] = explode('_', $callsign, 2);

        $airportMap = config('bookingairports');
        $mapping = $airportMap[$icao] ?? null;

        if (!$mapping) return false;

        if (str_starts_with($position, 'F_')) {
            return !empty($roster->fss) && $roster->fss > 0;
        }

        foreach ($mapping['positions'] as $pos) {
            if (str_contains($position, $pos)) {
                foreach ($mapping['columns'] as $column) {
                    if (!empty($roster->$column) && $roster->$column > 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

