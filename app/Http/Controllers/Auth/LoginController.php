<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use App\Models\Users\UserPreferences;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Vatsim\OAuth2\Client\Provider\Vatsim;

/**
 * Class LoginController.
 */
class LoginController extends Controller
{
    /**
     * LoginController Contructor
     */
    public function AuthLogin()
    {
        $provider = new Vatsim([
            'clientId'     => env('VATSIM_CLIENT_ID'),
            'clientSecret' => env('VATSIM_CLIENT_SECRET'),
            'redirectUri'  => env('VATSIM_REDIRECT_URI'),
        ]);

        $authorizationUrl = $provider->getAuthorizationUrl([
            'scope' => 'full_name vatsim_details email',
        ]);

        session(['oauth2state' => $provider->getState()]);

        return redirect($authorizationUrl);
    }

    /**
     * Validate the VATSIM OAuth2 login and log the user in.
     */
    public function validateAuthLogin(Request $request)
    {
        $provider = new Vatsim([
            'clientId'     => env('VATSIM_CLIENT_ID'),
            'clientSecret' => env('VATSIM_CLIENT_SECRET'),
            'redirectUri'  => env('VATSIM_REDIRECT_URI'),
        ]);

        if (empty($request->state) || $request->state !== session('oauth2state')) {
            session()->forget('oauth2state');
            abort(403, 'Invalid state');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', ['code' => $request->code]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return redirect('/')->with('error', 'Failed to get access token: ' . $e->getMessage());
        }

        session([
            'vatsim_token' => $token->getToken(),
            'vatsim_refresh_token' => $token->getRefreshToken(),
            'vatsim_token_expires' => $token->getExpires(),
        ]);

        if (session('vatsim_token_expires') && session('vatsim_token_expires') < time()) {
            $token = $provider->getAccessToken('refresh_token', [
                'refresh_token' => session('vatsim_refresh_token'),
            ]);
            session([
                'vatsim_token' => $token->getToken(),
                'vatsim_refresh_token' => $token->getRefreshToken(),
                'vatsim_token_expires' => $token->getExpires(),
            ]);
        }

        try {
            $AuthUser = $provider->getResourceOwner($token);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to fetch user details: ' . $e->getMessage());
        }

        $user = User::updateOrCreate(['id' => $AuthUser->id], [
            'email' => $AuthUser->email,
            'fname' => utf8_decode($AuthUser->name_first),
            'lname' => $AuthUser->name_last,
            'rating_id' => $AuthUser->rating->id,
            'rating_short' => $AuthUser->rating->short,
            'rating_long' => $AuthUser->rating->long,
            'rating_GRP' => $AuthUser->rating->GRP,
            'reg_date' => $AuthUser->reg_date,
            'subdivision_code' => $AuthUser->subdivision->code,
            'subdivision_name' => $AuthUser->subdivision->name,
            'display_fname' => $AuthUser->name_first,
        ]);

        Auth::login($user, true);

        UserPreferences::firstOrCreate(['user_id' => $user->id]);

        return redirect()->intended('/')->with('success', 'Logged in!');
    }

    /**
     * Log the user out.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/')->with('success', 'Logged out!');
    }

    /*
    Connect integration
    */
    public function connectLogin()
    {
        session()->forget('state');
        session()->forget('token');
        session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => config('connect.client_id'),
            'redirect_uri' => config('connect.redirect'),
            'response_type' => 'code',
            'scope' => 'full_name vatsim_details email',
            'required_scopes' => 'vatsim_details',
            'state' => $state,
        ]);

        return redirect(config('connect.url').'/oauth/authorize?'.$query);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function validateConnectLogin(Request $request)
    {
        //Written by Harrison Scott
        $http = new Client;

        try {
            $response = $http->post(config('connect.url').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('connect.client_id'),
                    'client_secret' => config('connect.secret'),
                    'redirect_uri' => config('connect.redirect'),
                    'code' => $request->code,
                ],
            ]);
        } catch (ClientException $e) {
            return redirect()->route('index')->with('error-modal', $e->getResponse()->getBody());
        }

        session()->put('connect_token', json_decode((string) $response->getBody(), true));

        try {
            $response = (new \GuzzleHttp\Client)->get(config('connect.url').'/api/user', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.session()->get('connect_token.access_token'),
                ],
            ]);
        } catch (ClientException $e) {
            return redirect()->back()->with('error-modal', $e->getResponse()->getBody());
        }

        $response = json_decode($response->getBody());

        if (! isset($response->data->cid)) {
            return redirect()->route('index')->with('error-modal', 'There was an error processing data from Connect (No CID)');
        }

        if (! isset($response->data->vatsim->rating)) {
            return redirect()->route('index')->with('error-modal', 'We cannot create an account without VATSIM details.');
        }

        $checkrating = RosterMember::where('cid', $response->data->cid)->first();
        if ($checkrating != null) {
            if ($checkrating->rating != $response->data->vatsim->rating->short) {
                $checkrating->rating_hours = 0;
                $checkrating->save();
            }
        }

        $user = User::updateOrCreate(['id' => $response->data->cid], [
            'email' => isset($response->data->personal->email) ? $response->data->personal->email : 'no-reply@czvr.ca',
            'fname' => isset($response->data->personal->name_first) ? utf8_decode($response->data->personal->name_first) : $response->data->cid,
            'lname' => isset($response->data->personal->name_last) ? $response->data->personal->name_last : $response->data->cid,
            'rating_id' => $response->data->vatsim->rating->id,
            'rating_short' => $response->data->vatsim->rating->short,
            'rating_long' => $response->data->vatsim->rating->long,
            'rating_GRP' => $response->data->vatsim->rating->long,
            'reg_date' => null,
            'region_code' => $response->data->vatsim->region->id,
            'region_name' => $response->data->vatsim->region->name,
            'division_code' => $response->data->vatsim->division->id,
            'division_name' => $response->data->vatsim->division->name,
            'used_connect' => true,
        ]);


        if ($user->display_fname === null) {
            $user->display_fname = isset($response->data->personal->name_first) ? utf8_decode($response->data->personal->name_first) : $response->data->cid;
        }

        if (! isset($response->data->personal->name_first)) {
            $user->display_cid_only = true;
        }

        $user->save();

        Auth::login($user, true);
        if (! UserPreferences::where('user_id', $user->id)->first()) {
            $prefs = new UserPreferences();
            $prefs->user_id = $user->id;
            $prefs->ui_mode = 'light';
            $prefs->save();
        }

        return redirect()->intended('/')->with('success', 'Logged in!');
    }
}
