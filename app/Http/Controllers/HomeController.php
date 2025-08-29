<?php

namespace App\Http\Controllers;

use App\Classes\VatsimHelper;
use App\Models\Events\Event;
use App\Models\Network\SessionLog;
use App\Models\News\News;
use App\Models\Settings\HomepageImages;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class HomeController extends Controller
{
    public function view()
    {
        $finalPositions = [];
        $news = collect();
        $nextEvents = collect();
        $topControllersArray = [];
        $weather = [];
        $background = null;

        // Vancouver online controllers
        try {
            $client = new Client();
            $response = $client->request('GET', VatsimHelper::getDatafeedUrl());
            $controllers = json_decode($response->getBody()->getContents());

            if (isset($controllers->controllers)) {
                $prefixes = ['CZVR_', 'ZVR_', 'CYVR_', 'CYYJ_', 'CYLW_', 'CYXS_', 'CYXX_', 'CYCD_'];
                foreach ($controllers->controllers as $c) {
                    if (
                        isset($c->callsign, $c->facility) &&
                        Str::startsWith($c->callsign, $prefixes) &&
                        !Str::endsWith($c->callsign, ['ATIS', 'OBS']) &&
                        $c->facility != 0
                    ) {
                        $finalPositions[] = $c;
                    }
                }
            }
        } catch (Exception $e) {
            \Log::error('Failed to fetch VATSIM controllers: '.$e->getMessage());
        }

        // News
        try {
            $news = News::where('visible', true)->get()->sortByDesc('published')->take(3);
        } catch (Exception $e) {
            \Log::error('Failed to fetch news: '.$e->getMessage());
        }

        // Events
        try {
            $nextEvents = Event::where('start_timestamp', '>', Carbon::now())
                               ->get()->sortBy('start_timestamp')->take(3);
        } catch (Exception $e) {
            \Log::error('Failed to fetch events: '.$e->getMessage());
        }

        // Top Controllers
        try {
            $colourArray = ['#6CC24A', '#B2D33C', '#E3B031', '#F15025', '#8C8C8C'];
            $monthStart = Carbon::now()->startOfMonth()->toISOString();
            $monthEnd = Carbon::now()->endOfMonth()->toISOString();

            $topControllers = SessionLog::selectRaw('sum(duration) as duration, cid')
                                        ->whereBetween('session_start', [$monthStart, $monthEnd])
                                        ->groupBy('cid')
                                        ->get()->sortByDesc('duration')->take(5);

            $n = -1;
            foreach ($topControllers as $top) {
                $topControllersArray[] = [
                    'id' => $n += 1,
                    'cid' => $top->cid ?? 'N/A',
                    'time' => function_exists('decimal_to_hm') ? decimal_to_hm($top->duration ?? 0) : 0,
                    'colour' => $colourArray[$n] ?? '#000000',
                ];
            }
        } catch (Exception $e) {
            \Log::error('Failed to fetch top controllers: '.$e->getMessage());
        }

        // Weather
        try {
            $weather = Cache::remember('weather.data', 900, function () {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.checkwx.com/metar/CYVR,CYYJ,CYLW,CYXS,CYXX,CYQQ/decoded?pretty=1');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: '.env('AIRPORT_API_KEY')]);

                $resp = json_decode(curl_exec($ch));
                curl_close($ch);

                $weatherArray = [];

                if (!empty($resp->data)) {
                    foreach ($resp->data as $w) {
                        $icao = $w->icao ?? 'UNKNOWN';
                        $w->flight_category = $w->flight_category ?? 'N/A';
                        $w->temperature = $w->temperature ?? null;
                        $w->wind = $w->wind ?? null;

                        switch ($icao) {
                            case 'CYVR': $weatherArray[0] = $w; break;
                            case 'CYYJ': $weatherArray[1] = $w; break;
                            case 'CYLW': $weatherArray[2] = $w; break;
                            case 'CYXS': $weatherArray[3] = $w; break;
                            case 'CYXX': $weatherArray[4] = $w; break;
                            case 'CYQQ': $weatherArray[5] = $w; break;
                            default: $weatherArray[] = (object)['error' => 'No weather data']; break;
                        }
                    }
                }

                ksort($weatherArray);
                return $weatherArray;
            });
        } catch (Exception $e) {
            \Log::error('Failed to fetch weather: '.$e->getMessage());
            $weather = [];
        }

        // Background Image
        try {
            $background = HomepageImages::all()->random();
        } catch (Exception $e) {
            \Log::error('Failed to fetch background image: '.$e->getMessage());
            $background = null;
        }

        return view('index', compact('finalPositions', 'news', 'nextEvents', 'topControllersArray', 'weather', 'background'));
    }
}
