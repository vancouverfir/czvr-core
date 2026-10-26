@extends('layouts.master')

@section('title', 'Vancouver FIR')
@section('description', 'Welcome to Vancouver - located in the left of Canada on the VATSIM network!')

@push('styles')
    <link href="{{ asset('css/pages/homepage.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/leaflet/leaflet.css') }}" rel="stylesheet">
@endpush

@section('content')
<main>
    <div class="home-landing">
        <section class="home-hero">
            @if ($backgroundUrl)
                <img class="homepage-background is-active" src="{{ $backgroundUrl }}" alt="" fetchpriority="high" draggable="false">
                <img class="homepage-background" src="" alt="" draggable="false">
            @endif
            <div class="container homepage-hero-inner">
                <div class="homepage-hero-copy">
                    <span class="homepage-hero-leaf" aria-hidden="true"><i class="fab fa-canadian-maple-leaf"></i></span>
                    <h1>From Sea to Sky!</h1>
                    <p>Discover virtual aviation across Canada’s spectacular West Coast!</p>
                    <a class="home-explore-link" href="#homeOverview"><span>Come explore Canada’s West Coast!</span> <i class="fas fa-arrow-down" aria-hidden="true"></i></a>
                </div>
            </div>
            @if ($background)
                <span class="home-inline-credit">Screenshot by {{ $background->credit }}</span>
            @endif
        </section>
    </div>
    <section class="home-overview" id="homeOverview" aria-label="Vancouver FIR live information">
        <div class="home-overview-inner">
            <div class="home-overview-layout">
                <div class="home-overview-grid">
                    <div class="home-overview-column">
                        <h3 class="home-section-heading">Online ATC</h3>
                        @forelse ($finalPositions as $position)
                            <div class="home-info-row">
                                <a href="{{ url('/roster/'.$position->cid) }}" target="_blank" rel="noopener" class="home-info-primary">{{ $position->name == $position->cid ? $position->name : $position->name.' '.$position->cid }}</a>
                                <span class="badge main-colour">{{ $position->callsign }}</span>
                            </div>
                        @empty
                            <p class="home-empty">No controllers are currently online!</p>
                        @endforelse
                    </div>

                    <div class="home-overview-column">
                        <h3 class="home-section-heading">Weather</h3>
                        @forelse ($weather as $report)
                            <div class="home-info-row home-weather-row">
                                <div class="home-weather-badges">
                                    <span class="badge home-weather-category {{ $report->flight_category }}">{{ $report->flight_category }}</span>
                                    @if (\Carbon\Carbon::make($report->observed) < now()->subHours(2))
                                        <span class="badge home-weather-category grey">OUTDATED</span>
                                    @endif
                                </div>
                                <div class="home-weather-content">
                                    <span class="home-info-primary">{{ $report->icao }} — {{ $report->station->name }}</span>
                                    <div class="home-weather-report">{{ $report->raw_text }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="home-empty">Weather data is temporarily unavailable!</p>
                        @endforelse
                    </div>

                    <div class="home-overview-column">
                        <h3 class="home-section-heading">Upcoming Events</h3>
                        @forelse ($nextEvents as $event)
                            @php
                                $eventStart = \Carbon\Carbon::parse($event->start_timestamp);
                                $eventEnd = \Carbon\Carbon::parse($event->end_timestamp);
                                $eventIsLive = now()->between($eventStart, $eventEnd);
                            @endphp
                            <div class="home-info-row"><a href="{{ url('/events/'.$event->slug) }}" class="home-info-primary">{{ $event->name }}</a><span class="home-info-secondary {{ $eventIsLive ? 'home-event-live' : '' }}">{{ $eventIsLive ? 'Happening Now!' : $eventStart->format('M j, Y H:i T') }}</span></div>
                        @empty
                            <p class="home-empty">No upcoming events!</p>
                        @endforelse
                    </div>

                    <div class="home-overview-column">
                        <h3 class="home-section-heading">Latest News</h3>
                        @forelse ($news as $article)
                            <div class="home-info-row"><a href="{{ url('/news/'.$article->slug) }}" class="home-info-primary">{{ $article->title }}</a><span class="home-info-secondary">{{ $article->posted_on_pretty() }}</span></div>
                        @empty
                            <p class="home-empty">No current news!</p>
                        @endforelse
                    </div>

                    <div class="home-overview-column">
                        <h3 class="home-section-heading">Top Controllers This Month</h3>
                        @php $rankedControllers = collect($topControllersArray)->filter(fn ($controller) => $controller['time'] != 0); @endphp
                        @if ($rankedControllers->isNotEmpty())
                            <div class="home-ranking-list">
                                @foreach ($rankedControllers as $controller)
                                    <div class="home-rank">
                                        <span class="home-rank-number">{{ $loop->iteration }}</span>
                                        <span class="home-rank-name">{{ $controller['name'] }}</span>
                                        <span class="home-rank-time">{{ $controller['time'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="home-empty">No controller hours recorded this month!</p>
                        @endif
                    </div>
                </div>

                <div class="home-livemap" id="liveMap" role="region" aria-labelledby="live-map-title">
                    <h3 id="live-map-title" class="home-section-heading">Live Map</h3>

                    <div class="map-shell">
                        <div class="map-meta">
                            <div class="map-legend" aria-label="Map legend">
                                <span class="map-legend-item"><span class="map-legend-dot"></span>Available gate</span>
                                <span class="map-legend-item"><span class="map-legend-dot taken"></span>Occupied gate</span>
                                <span class="map-legend-item"><span class="map-legend-plane">✈</span>Aircraft</span>
                            </div>
                            <div class="live-map-airports" role="tablist" aria-label="Select an airport">
                                <button type="button" class="active" data-airport="CYVR" role="tab" aria-selected="true">Vancouver (CYVR)</button>
                                <button type="button" data-airport="CYYJ" role="tab" aria-selected="false">Victoria (CYYJ)</button>
                                <button type="button" data-airport="CYLW" role="tab" aria-selected="false">Kelowna (CYLW)</button>
                            </div>
                            <div id="map-status" class="map-status" role="status">Loading live traffic…</div>
                        </div>
                        <div id="map" data-feed-url="{{ route('livemap.data') }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script id="homepageBackgrounds" type="application/json">@json($backgroundImages)</script>
<script src="{{ asset('js/homepage.js') }}" defer></script>
<script src="{{ asset('vendor/leaflet/leaflet.js') }}" defer></script>
<script src="{{ asset('js/livemap.js') }}?v={{ filemtime(public_path('js/livemap.js')) }}" defer></script>
@stop
