<?php

namespace App\Http\Controllers;

use App\Classes\VatsimHelper;
use App\Models\Events\Event;
use App\Models\Network\SessionLog;
use App\Models\News\News;
use App\Models\Settings\HomepageImages;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $banner = DB::table('core_info')->first();

        // Vancouver Online Controllers
        try {
            $finalPositions = Cache::remember('vatsim.controllers', 300, function () {
                $client = new Client();
                $response = $client->request('GET', VatsimHelper::getDatafeedUrl());
                $controllers = json_decode($response->getBody()->getContents());

                $finalPositions = [];
                if (isset($controllers->controllers)) {
                    $prefixes = ['CZVR_', 'ZVR_', 'CYVR_', 'CYYJ_', 'CYLW_', 'CYXS_', 'CYXX_', 'CYCD_'];
                    foreach ($controllers->controllers as $c) {
                        if (
                            isset($c->callsign, $c->facility) &&
                            Str::startsWith($c->callsign, $prefixes) &&
                            ! Str::endsWith($c->callsign, ['ATIS', 'OBS']) &&
                            $c->facility != 0
                        ) {
                            $finalPositions[] = $c;
                        }
                    }
                }

                return $finalPositions;
            });
        } catch (Exception $e) {
            \Log::error('Failed to fetch VATSIM controllers: '.$e->getMessage());
        }

        // News
        try {
            $news = News::where('visible', true)
                        ->orderBy('published', 'desc')
                        ->take(3)
                        ->get();
        } catch (Exception $e) {
            \Log::error('Failed to fetch news: '.$e->getMessage());
        }

        // Events
        try {
            $nextEvents = Event::where('end_timestamp', '>', now())
                               ->orderBy('end_timestamp')
                               ->take(3)
                               ->get();

            $now = Carbon::now('UTC');

            $ongoingEvent = $nextEvents->first(function ($event) use ($now) {
                return $now->between(
                    Carbon::parse($event->start_timestamp),
                    Carbon::parse($event->end_timestamp)
                );
            });

            if ($ongoingEvent) {
                DB::table('core_info')->update([
                    'banner' => "🎉 Happening Now! {$ongoingEvent->name}! 🎉",
                    'bannerLink' => url('/events/'.$ongoingEvent->slug),
                    'bannerMode' => 'success',
                    'updated_at' => now(),
                ]);

                $banner->banner = "🎉 Happening Now! {$ongoingEvent->name}! 🎉";
                $banner->bannerLink = url('/events/'.$ongoingEvent->slug);
                $banner->bannerMode = 'success';
            } else {
                DB::table('core_info')->update([
                    'banner' => '',
                    'bannerLink' => '',
                    'bannerMode' => '',
                    'updated_at' => now(),
                ]);

                $banner->banner = '';
                $banner->bannerLink = '';
                $banner->bannerMode = '';
            }
        } catch (Exception $e) {
            \Log::error('Failed to fetch events: '.$e->getMessage());
        }

        // Top Controllers
        try {
            $colourArray = ['#6CC24A', '#B2D33C', '#E3B031', '#F15025', '#8C8C8C'];
            $monthStart = now()->startOfMonth()->toISOString();
            $monthEnd = now()->endOfMonth()->toISOString();

            $topControllers = SessionLog::selectRaw('cid, sum(duration) as duration')
                                        ->whereBetween('session_start', [$monthStart, $monthEnd])
                                        ->groupBy('cid')
                                        ->orderByDesc('duration')
                                        ->take(5)
                                        ->get();

            foreach ($topControllers as $index => $top) {
                $topControllersArray[] = [
                    'id' => $index,
                    'cid' => $top->cid ?? 'N/A',
                    'time' => function_exists('decimal_to_hm') ? decimal_to_hm($top->duration ?? 0) : 0,
                    'colour' => $colourArray[$index] ?? '#000000',
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

                if (! empty($resp->data)) {
                    foreach ($resp->data as $w) {
                        $icao = $w->icao ?? 'UNKNOWN';
                        $w->flight_category = $w->flight_category ?? 'N/A';
                        $w->temperature = $w->temperature ?? null;
                        $w->wind = $w->wind ?? null;

                        switch ($icao) {
                            case 'CYVR': $weatherArray[0] = $w;
                                break;
                            case 'CYYJ': $weatherArray[1] = $w;
                                break;
                            case 'CYLW': $weatherArray[2] = $w;
                                break;
                            case 'CYXS': $weatherArray[3] = $w;
                                break;
                            case 'CYXX': $weatherArray[4] = $w;
                                break;
                            case 'CYQQ': $weatherArray[5] = $w;
                                break;
                            default: $weatherArray[] = (object) ['error' => 'No weather data'];
                                break;
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

        // Random Background Image
        try {
            $background = HomepageImages::inRandomOrder()->first();
        } catch (Exception $e) {
            \Log::error('Failed to fetch background image: '.$e->getMessage());
            $background = null;
        }

        return view('index', compact('finalPositions', 'news', 'nextEvents', 'topControllersArray', 'weather', 'background', 'banner'));
    }
}
