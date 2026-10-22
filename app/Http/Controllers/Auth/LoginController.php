<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function AuthLogin(): RedirectResponse
    {
        Session::forget(['connect_state', 'connect_token']);

        $state = Str::random(40);
        Session::put('connect_state', $state);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('connect.client_id'),
            'redirect_uri' => config('connect.redirect'),
            'scope' => 'full_name vatsim_details email',
            'state' => $state,
            'prompt' => 'consent',
        ]);

        return redirect(config('connect.url').'/oauth/authorize?'.$query);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Logged out!');
    }

    public function validateAuthLogin(Request $request): RedirectResponse
    {
        $expectedState = (string) $request->session()->get('connect_state', '');
        $providedState = (string) $request->query('state', '');

        if ($expectedState === '' || $providedState === '' || ! hash_equals($expectedState, $providedState)) {
            return redirect()->route('index')
                ->with('error-modal', 'The authentication request could not be verified! Please try logging in again!');
        }

        $request->session()->forget('connect_state');

        if (! $request->filled('code')) {
            return redirect()->route('index')->with('info', 'Login cancelled!');
        }

        try {
            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post(config('connect.url').'/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('connect.client_id'),
                    'client_secret' => config('connect.secret'),
                    'redirect_uri' => config('connect.redirect'),
                    'code' => $request->string('code')->toString(),
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('VATSIM OAuth token endpoint was unavailable!', ['message' => $exception->getMessage()]);

            return redirect()->route('index')->with('error-modal', 'The authentication service is unavailable! Please try again!');
        }

        $accessToken = $tokenResponse->json('access_token');

        if (! $tokenResponse->successful() || ! is_string($accessToken) || $accessToken === '') {
            Log::warning('VATSIM OAuth token request failed!', ['status' => $tokenResponse->status()]);

            return redirect()->route('index')->with('error-modal', 'Authentication could not be completed! Please try again!');
        }

        try {
            $profileResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->get(config('connect.url').'/api/user');
        } catch (ConnectionException $exception) {
            Log::warning('VATSIM OAuth profile endpoint was unavailable!', ['message' => $exception->getMessage()]);

            return redirect()->route('index')->with('error-modal', 'The authentication service is unavailable! Please try again!');
        }

        if (! $profileResponse->successful()) {
            Log::warning('VATSIM OAuth profile request failed.', ['status' => $profileResponse->status()]);

            return redirect()->route('index')->with('error-modal', 'Your VATSIM profile could not be loaded!');
        }

        $profile = $profileResponse->object();
        $cid = data_get($profile, 'data.cid');
        $rating = data_get($profile, 'data.vatsim.rating');

        if (! $cid) {
            return redirect()->route('index')->with('error-modal', 'There was an error processing data from Connect (No CID)!');
        }

        if (! $rating) {
            return redirect()->route('index')->with('error-modal', 'We cannot create an account without VATSIM details!');
        }

        $rosterMember = RosterMember::where('cid', $cid)->first();
        $previousRating = User::whereKey($cid)->value('rating_short');
        if ($rosterMember && $previousRating !== null && $previousRating !== $rating->short) {
            $rosterMember->rating_hours = 0;
            $rosterMember->save();
        }

        $firstName = data_get($profile, 'data.personal.name_first') ?: (string) $cid;
        $lastName = data_get($profile, 'data.personal.name_last') ?: (string) $cid;

        $user = User::updateOrCreate(['id' => $cid], [
            'email' => data_get($profile, 'data.personal.email') ?: "no-reply+{$cid}@invalid.local",
            'fname' => $firstName,
            'lname' => $lastName,
            'rating_id' => $rating->id,
            'rating_short' => $rating->short,
            'rating_long' => $rating->long,
            'rating_GRP' => $rating->long,
            'reg_date' => null,
            'region_code' => data_get($profile, 'data.vatsim.region.id'),
            'region_name' => data_get($profile, 'data.vatsim.region.name'),
            'division_code' => data_get($profile, 'data.vatsim.division.id'),
            'division_name' => data_get($profile, 'data.vatsim.division.name'),
            'used_connect' => true,
        ]);

        if ($user->display_fname === null) {
            $user->display_fname = $firstName;
        }

        if (! data_get($profile, 'data.personal.name_first')) {
            $user->display_cid_only = true;
        }

        $user->save();
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Logged in!');
    }
}
