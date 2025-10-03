@extends('layouts.master')

@section('description', 'Welcome to Vancouver - located in the left of Canada on the VATSIM network!')

<style>
@media (min-width: 769px) {
    .card-background {
        height: 210px;
    }
}

@media (max-width: 769px) {
    .mobile-container {
        padding: 15px !important;
    }
}

.navbar .container {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
}
</style>

@section('content')
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
            <div class="winnipeg-blue"
                style="z-index: -1; width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; background-image: url({{ $background->url }}); background-size: cover; background-position: center; animation: heroZoom 10s ease-in-out infinite alternate;">
            </div>

            <style>
                @keyframes heroZoom {0% { transform: scale(1); }100% { transform: scale(1.03); }}
            </style>

            <div class="mobile-container container" style="text-align: center; padding-top: 30px; padding-bottom: 30px;">
                <div style="display: inline-block; position: relative; z-index: 1; padding: 30px;">
                    <h1 class="vancouver-text display-3" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); color: #fff;">
                        <span class="corner">From Sea to Sky!</span>
                    </h1>
                    <h4 class="vancouver-text mt-2" style="text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.1);">
                        <a href="#A" id="discoverMore" class="blue-text" style="color: #fff; text-decoration: none;">Come explore Canada's West Coast<i class="fas fa-arrow-down ml-2"></i></a>
                    </h4>
                    <small style="color: #fff; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">
                        <i>Screenshot by {{$background->credit}}</i>
                    </small>
                </div>
            </div>

            <div class="mobile-container container mt-4" style="padding: 90px" id="A">
                <div class="row">
                    <!-- First column -->
                    <div class="col-md-6">
                    <!-- Top Controllers this Month -->
                    <div class="card card-background mb-4" style="opacity: 90%; height: 210px; display: flex; flex-direction: column; overflow-y: auto;">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-award mr-2"></i>Top Controllers this Month</h2>
                        </div>
                        <div class="card-body">
                            @if(count($topControllersArray) == 0)
                                <h5 class="text-colour text-center">No Data Yet</h5>
                            @endif
                            @foreach($topControllersArray as $t)
                                @if($t['time'] != 0)
                                    <div class="badge w-100 d-flex justify-content-between align-items-center py-1 px-2 mb-1" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9); font-weight: normal; opacity: 90%; background-color: {{$t['colour']}} !important; font-size: 0.7rem; background-image: linear-gradient(-45deg, rgba(255,255,255,0) 25%, rgba(0,0,0,0.15) 25%, rgba(0,0,0,0.15) 50%, rgba(255,255,255,0) 50%, rgba(255,255,255,0) 75%, rgba(0,0,0,0.15) 75%, rgba(0,0,0,0.15) 100%); background-size: 20px 20px; border: 1px solid rgba(0, 0, 0, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.7);">
                                        <div>{{User::where('id', $t['cid'])->first()->fullName('FLC')}}</div>
                                        <div>{{$t['time']}}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Second column -->
                <div class="col-md-6">
                    <!-- Online Controllers -->
                    <div class="card card-background mb-4" style="opacity: 90%; height: 210px; display: flex; flex-direction: column; overflow-y: auto;">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold blue-text card-header-size text-center"><i class="fas fa-user mr-2"></i>Online Controllers</h2>
                        </div>
                        <div class="card-body">
                            @if(count($finalPositions) == 0)
                                <h5 class="text-colour text-center">No Controllers Online – See Controller <a class="text-white" href="https://booking.czvr.ca">Bookings!</h5>
                            @endif
                            @foreach($finalPositions as $p)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="https://czvr.ca/roster/{{$p->cid}}" target="_blank" class="text-colour">
                                        @if($p->name == $p->cid)
                                            <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}}
                                        @else
                                            <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}} {{$p->cid}}
                                        @endif
                                    </a>
                                    <span class="badge main-colour">{{$p->callsign}}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- First column -->
                <div class="col-md-6">
                    <!-- Recent News -->
                    <div class="card card-background mb-4" style="opacity: 90%; height: 210px; display: flex; flex-direction: column; overflow-y: auto;">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-newspaper mr-2"></i>Recent News</h2></a>
                        </div>
                        <div class="card-body">
                            @if(count($news) == 0)
                                <h5 class="text-colour text-center">No Current News</h5>
                            @endif
                            @foreach($news as $n)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge text-colour mr-2">{{$n->posted_on_pretty()}}</span>
                                    <a href="{{url('/news').'/'.$n->slug}}" class="text-colour">{{$n->title}}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Second column -->
                <div class="col-md-6">
                    <!-- Upcoming Events -->
                    <div class="card card-background mb-4" style="opacity: 90%; height: 210px; display: flex; flex-direction: column; overflow-y: auto;">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-calendar-alt mr-2"></i>Upcoming Events</h2>
                        </div>
                        <div class="card-body">
                            @if(count($nextEvents) == 0)
                                <h5 class="text-colour text-center">Stay tuned here for Upcoming Events!</h5>
                            @endif
                            @foreach($nextEvents as $e)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $start = \Carbon\Carbon::parse($e->start_timestamp);
                                    $end = \Carbon\Carbon::parse($e->end_timestamp);

                                    $HappeningNow = $now->between($start, $end);
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ url('/events').'/'.$e->slug }}" class="text-colour">
                                        {{$e->name}}
                                    </a>
                                    <span class="badge {{ $HappeningNow ? 'badge-success' : 'main-colour' }}">
                                        {{ $HappeningNow ? 'Happening Now!' : $start->format('M d, Y H:i T') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-background" style="opacity: 90%; margin-bottom: 30px;">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-sun mr-2"></i>Weather</h2>
                        </div>
                        <div class="card-body">
                            @if(count($weather) == 0)
                                <h5 class="text-colour text-center">No weather data.</h5>
                            @endif
                            <div class="row">
                                @foreach($weather as $w)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="mb-0 mr-2">{{$w->icao}} - {{$w->station->name}}</h5>
                                        <span class="badge {{$w->flight_category}}">{{$w->flight_category}}</span>
                                        @if(Carbon\Carbon::make($w->observed) < Carbon\Carbon::now()->subHours(2))
                                            <span class="badge grey ml-1">OUTDATED</span>
                                        @endif
                                    </div>
                                    <div class="small">{{$w->raw_text}}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
