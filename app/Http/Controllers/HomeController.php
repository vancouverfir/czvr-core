<?php

namespace App\Http\Controllers;

use App\Models\Events\Event;
use App\Models\Network\SessionLog;
use App\Models\News\News;
use App\Models\Settings\HomepageImages;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function view()
    {
        // VATSIM Controllers (from cache)
        $finalPositions = Cache::get('vatsim.controllers', []);

        // Weather (from cache)
        $weather = Cache::get('weather.data', []);

        $banner = DB::table('core_info')->first();

        // Load cached data or default to empty collections
        $finalPositions = Cache::get('vatsim.controllers', []);
        $news = Cache::get('home.news', collect());
        $nextEvents = Cache::get('home.events', collect());
        $topControllersArray = Cache::get('home.topControllers', []);
        $weather = Cache::get('weather.data', []);
        $background = Cache::get('home.background', null);

        // Background image
        if (! $background) {
            try {
                $background = HomepageImages::inRandomOrder()->first();
            } catch (Exception $e) {
                \Log::error('Failed to fetch background image: '.$e->getMessage());
            }
        }

        // News (cache for 5 min)
        if ($news->isEmpty()) {
            try {
                $news = News::where('visible', true)
                            ->orderBy('published', 'desc')
                            ->take(3)
                            ->get();
                Cache::put('home.news', $news, 300);
            } catch (Exception $e) {
                \Log::error('Failed to fetch news: '.$e->getMessage());
            }
        }

        // Upcoming events (cache for 5 min)
        if ($nextEvents->isEmpty()) {
            try {
                $nextEvents = Event::where('end_timestamp', '>', now())
                                   ->orderBy('end_timestamp')
                                   ->take(3)
                                   ->get();
                Cache::put('home.events', $nextEvents, 300);
            } catch (Exception $e) {
                \Log::error('Failed to fetch events: '.$e->getMessage());
            }
        }

        // Top Controllers (cache for 15 min)
        if (empty($topControllersArray)) {
            try {
                $monthStart = now()->startOfMonth()->toISOString();
                $monthEnd = now()->endOfMonth()->toISOString();
                $colourArray = ['#6CC24A', '#B2D33C', '#E3B031', '#F15025', '#8C8C8C'];

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

                Cache::put('home.topControllers', $topControllersArray, 900);
            } catch (Exception $e) {
                \Log::error('Failed to fetch top controllers: '.$e->getMessage());
            }
        }

        // Banner update (only if needed)
        try {
            $now = Carbon::now('UTC');

            $ongoingEvent = $nextEvents->first(function ($event) use ($now) {
                return $now->between(
                    Carbon::parse($event->start_timestamp),
                    Carbon::parse($event->end_timestamp)
                );
            });

            $isEventBanner = str_contains($banner->banner, 'Happening Now!');

            $isCustomBanner = !empty($banner->banner) && !$isEventBanner;

            if ($isCustomBanner) {
            } elseif ($ongoingEvent) {
                $banner->banner = "🎉 Happening Now! {$ongoingEvent->name}! 🎉";
                $banner->bannerLink = url('/events/'.$ongoingEvent->slug);
                $banner->bannerMode = 'success';

                DB::table('core_info')->update([
                    'banner' => $banner->banner,
                    'bannerLink' => $banner->bannerLink,
                    'bannerMode' => $banner->bannerMode,
                    'updated_at' => now(),
                ]);

            } elseif ($isEventBanner) {
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
            \Log::error('Failed to update banner: '.$e->getMessage());
        }

        return view('index', compact('finalPositions', 'news', 'nextEvents', 'topControllersArray', 'weather', 'background', 'banner'));
    }
}
