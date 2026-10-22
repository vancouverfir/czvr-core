<?php

namespace App\Http\Controllers;

use App\Models\Events\Event;
use App\Models\Network\SessionLog;
use App\Models\News\News;
use App\Models\Settings\CoreSettings;
use App\Models\Settings\HomepageImages;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    public function view(): View
    {
        $coreSettings = CoreSettings::query()->find(1) ?? new CoreSettings;

        $finalPositions = $this->cachedObjectRows('vatsim.controllers') ?? [];
        $weather = $this->cachedObjectRows('weather.data', false) ?? [];

        $newsRows = $this->cachedRows('home.news');
        if ($newsRows === null) {
            try {
                $newsRows = News::where('visible', true)
                    ->orderBy('published', 'desc')
                    ->take(3)
                    ->get()
                    ->map(fn (News $article): array => $article->getAttributes())
                    ->all();
                Cache::put('home.news', $newsRows, 300);
            } catch (Throwable $e) {
                Log::error('Failed to fetch news: '.$e->getMessage());
                $newsRows = [];
            }
        }
        $news = News::hydrate($newsRows);

        $eventRows = $this->cachedRows('home.events');
        if ($eventRows === null) {
            try {
                $eventRows = Event::where('end_timestamp', '>', now())
                    ->orderBy('end_timestamp')
                    ->take(3)
                    ->get()
                    ->map(fn (Event $event): array => $event->getAttributes())
                    ->all();
                Cache::put('home.events', $eventRows, 300);
            } catch (Throwable $e) {
                Log::error('Failed to fetch events: '.$e->getMessage());
                $eventRows = [];
            }
        }
        $nextEvents = Event::hydrate($eventRows);

        // Choose a fresh image on every visit and avoid immediately repeating the
        // previous one for this visitor
        Cache::forget('home.background');
        $background = null;
        $backgroundImages = [];

        try {
            $availableBackgrounds = HomepageImages::query()
                ->get()
                ->filter(fn (HomepageImages $image): bool => $image->isAvailable());
            $backgroundImages = $availableBackgrounds
                ->shuffle()
                ->map(fn (HomepageImages $image): array => [
                    'url' => $image->browserUrl(),
                    'credit' => $image->credit,
                ])
                ->values()
                ->all();
            $previousBackgroundId = session('home.previous_background_id');
            $backgroundChoices = $availableBackgrounds->count() > 1
                ? $availableBackgrounds->reject(fn (HomepageImages $image): bool => $image->getKey() === $previousBackgroundId)
                : $availableBackgrounds;
            $background = $backgroundChoices->isEmpty() ? null : $backgroundChoices->random();

            if ($background?->getKey() !== null) {
                session(['home.previous_background_id' => $background->getKey()]);
            }
        } catch (Throwable $e) {
            Log::error('Failed to fetch background image: '.$e->getMessage());
        }

        $backgroundUrl = $background?->browserUrl();

        $topControllersArray = $this->cachedRows('home.topControllers.v2');
        if ($topControllersArray === null) {
            try {
                $topControllersArray = [];
                $monthStart = now()->startOfMonth()->toISOString();
                $monthEnd = now()->endOfMonth()->toISOString();
                $colourArray = ['#6CC24A', '#B2D33C', '#E3B031', '#F15025', '#8C8C8C'];

                $topControllers = SessionLog::selectRaw('cid, sum(duration) as duration')
                    ->whereBetween('session_start', [$monthStart, $monthEnd])
                    ->groupBy('cid')
                    ->orderByDesc('duration')
                    ->take(5)
                    ->get();

                $controllerNames = User::whereIn('id', $topControllers->pluck('cid'))
                    ->get()
                    ->mapWithKeys(fn (User $user): array => [(string) $user->id => $user->fullName('FLC')]);

                foreach ($topControllers as $index => $top) {
                    $topControllersArray[] = [
                        'id' => $index,
                        'cid' => $top->cid ?? 'N/A',
                        'time' => function_exists('decimal_to_hm') ? decimal_to_hm($top->duration ?? 0) : 0,
                        'colour' => $colourArray[$index] ?? '#000000',
                        'name' => $controllerNames->get((string) $top->cid, (string) ($top->cid ?? 'Unknown')),
                    ];
                }

                Cache::put('home.topControllers.v2', $topControllersArray, 900);
            } catch (Throwable $e) {
                Log::error('Failed to fetch top controllers: '.$e->getMessage());
                $topControllersArray = [];
            }
        }

        // Event banners are request-only. We avoid overwriting manual banners
        try {
            $now = Carbon::now('UTC');

            $ongoingEvent = $nextEvents->first(function ($event) use ($now) {
                return $now->between(
                    Carbon::parse($event->start_timestamp),
                    Carbon::parse($event->end_timestamp)
                );
            });

            $isEventBanner = str_contains((string) $coreSettings->banner, 'Happening Now!');
            $isCustomBanner = ! empty($coreSettings->banner) && ! $isEventBanner;

            if (! $isCustomBanner && $ongoingEvent) {
                $coreSettings->banner = "Happening Now! {$ongoingEvent->name}!";
                $coreSettings->bannerLink = url('/events/'.$ongoingEvent->slug);
                $coreSettings->bannerMode = 'success';
            } elseif (! $isCustomBanner && $isEventBanner) {
                $coreSettings->banner = '';
                $coreSettings->bannerLink = '';
                $coreSettings->bannerMode = '';
            }
        } catch (Throwable $e) {
            Log::error('Failed to prepare event banner: '.$e->getMessage());
        }

        return view('index', compact('finalPositions', 'news', 'nextEvents', 'topControllersArray', 'weather', 'background', 'backgroundUrl', 'backgroundImages', 'coreSettings'));
    }

    private function cachedArray(string $key): ?array
    {
        try {
            $value = Cache::get($key);
        } catch (Throwable $e) {
            Log::warning('Failed to read cache payload!', ['key' => $key, 'message' => $e->getMessage()]);

            return null;
        }

        if ($value === null) {
            return null;
        }

        if (! is_array($value) || ! $this->isCacheSafe($value)) {
            $this->discardIncompatibleCache($key);

            return null;
        }

        return $value;
    }

    private function cachedRows(string $key, bool $requireList = true): ?array
    {
        $rows = $this->cachedArray($key);

        if ($rows === null) {
            return null;
        }

        if ($requireList && ! array_is_list($rows)) {
            $this->discardIncompatibleCache($key);

            return null;
        }

        foreach ($rows as $row) {
            if (! is_array($row)) {
                $this->discardIncompatibleCache($key);

                return null;
            }
        }

        return $rows;
    }

    private function cachedObjectRows(string $key, bool $requireList = true): ?array
    {
        $rows = $this->cachedRows($key, $requireList);

        if ($rows === null) {
            return null;
        }

        $objects = json_decode(
            json_encode(array_values($rows), JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        return is_array($objects) ? $objects : [];
    }

    private function isCacheSafe(mixed $value): bool
    {
        if (! is_array($value)) {
            return $value === null || is_scalar($value);
        }

        foreach ($value as $item) {
            if (! $this->isCacheSafe($item)) {
                return false;
            }
        }

        return true;
    }

    private function discardIncompatibleCache(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (Throwable $e) {
            Log::warning('Failed to discard incompatible cache payload!', ['key' => $key, 'message' => $e->getMessage()]);

            return;
        }

        Log::notice('Discarded incompatible legacy cache payload!', ['key' => $key]);
    }
}
