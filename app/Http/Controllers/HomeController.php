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

class HomeController extends Controller
{
    public function view()
    {
        //Vancouver online controllers
        $client = new Client();
        $response = $client->request('GET', VatsimHelper::getDatafeedUrl());
        $controllers = json_decode($response->getBody()->getContents())->controllers;

        $finalPositions = [];

        $prefixes = [

            'CZVR_',
            'ZVR_',
            'CYVR_',
            'CYYJ_',
            'CYLW_',
            'CYXS_',
            'CYXX_',
            'CYCD_',
        ];

        foreach ($controllers as $c) {
            if (Str::startsWith($c->callsign, $prefixes) && ! Str::endsWith($c->callsign, ['ATIS', 'OBS']) && $c->facility != 0) {
                $finalPositions[] = $c;
            }
        }

        //News
        $news = News::where('visible', true)->get()->sortByDesc('published')->take(3);

        //Event
        $nextEvents = Event::where('start_timestamp', '>', Carbon::now())->get()->sortBy('start_timestamp')->take(3);

        //Top Controllers
        $topControllersArray = [];

        $colourArray = [
            0 => '#6CC24A',
            1 => '#B2D33C',
            2 => '#E3B031',
            3 => '#F15025',
            4 => '#8C8C8C',
        ];

        $monthStart = Carbon::now()->startOfMonth()->toISOString();
        $monthEnd = Carbon::now()->endOfMonth()->toISOString();
        $topControllers = SessionLog::selectRaw('sum(duration) as duration, cid')
                                        ->whereBetween('session_start', [$monthStart, $monthEnd])
                                        ->groupBy('cid')
                                        ->get()->sortByDesc('duration')->take(5);

        $n = -1;
        foreach ($topControllers as $top) {

            $top = [
                'id' => $n += 1,
                'cid' => $top['cid'],
                'time' => decimal_to_hm($top['duration']),
                'colour' => $colourArray[$n],
            ];
            array_push($topControllersArray, $top);
        }

        //Weather
        $weather = Cache::remember('weather.data', 900, function () {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.checkwx.com/metar/CYVR,CYYJ,CYLW,CYXS,CYXX,CYQQ/decoded?pretty=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: '.env('AIRPORT_API_KEY')]);

            $resp = json_decode(curl_exec($ch));

            curl_close($ch);

            $weatherArray = [];

            if ($resp) {
                foreach ($resp->data as $w) {
                    switch ($w->icao) {
                        case 'CYVR':
                            $weatherArray[0] = $w;
                            break;
                        case 'CYYJ':
                            $weatherArray[1] = $w;
                            break;
                        case 'CYLW':
                            $weatherArray[2] = $w;
                            break;
                        case 'CYXS':
                            $weatherArray[3] = $w;
                            break;
                        case 'CYXX':
                            $weatherArray[4] = $w;
                            break;
                        case 'CYQQ':
                            $weatherArray[5] = $w;
                            break;
                    }
                }
            }

            ksort($weatherArray);

            return $weatherArray;
        });

        //Background Image
        $background = HomepageImages::all()->random();

        return view('index', compact('finalPositions', 'news', 'nextEvents', 'topControllersArray', 'weather', 'background'));
    }

    public function nate()
    {
        function getStuff($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $json = json_decode(curl_exec($ch));
            curl_close($ch);

            return $json;
        }

        $hours = getStuff('https://api.vatsim.net/api/ratings/1233493/rating_times/');

        $atcTime = decimal_to_hm($hours->atc);
        $pilotTime = decimal_to_hm($hours->pilot);
        $totalTime = decimal_to_hm($hours->atc + $hours->pilot);

        $timeOnNetwork = str_replace('T', ' ', getStuff('https://api.vatsim.net/api/ratings/1233493/')->reg_date);
        $yearsOnNetwork = Carbon::now()->diffInYears($timeOnNetwork);

        return view('nate', compact('atcTime', 'pilotTime', 'totalTime', 'yearsOnNetwork'));
    }
}
