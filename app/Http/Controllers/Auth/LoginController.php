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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function AuthLogin()
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
        ]);

        return redirect(config('connect.url').'/oauth/authorize?'.$query);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/')->with('success', 'Logged out!');
    }

    public function validateAuthLogin(Request $request)
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

        $tokenData = json_decode((string) $response->getBody(), true);
        Session::put('connect_token', $tokenData);

        try {
            $response = $http->get(config('connect.url').'/api/user', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$tokenData['access_token'],
                ],
            ]);
        } catch (ClientException $e) {
            return redirect()->back()->with('error-modal', $e->getResponse()->getBody());
        }

        $response = json_decode($response->getBody());

        $regDate = null;
        try {
            $coreResponse = $http->get('https://api.vatsim.net/v2/members/'.$response->data->cid, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $coreData = json_decode($coreResponse->getBody());
            $regDate = $coreData->data->reg_date ?? null;
        } catch (ClientException $e) {
        }

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
            'reg_date' => $regDate,
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
