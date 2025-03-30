@extends('layouts.master')
@section('description', 'Welcome to Vanouver - located in the left of Canada on the VATSIM network.')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
    <div>
        <div data-jarallax data-speed="0.2" class="jarallax d-flex align-items-center" style="height: min(calc((300vh - 59px) / 4), 1280px)">
            <div class="mask flex-center flex-column"
                 style="z-index: -1; width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; background-image: url({{$background->url}}); {{$background->css}}; background-size: cover; background-repeat: no-repeat; background-position: center;">
                <div class="container">
                    <div class="py-4 text-center">
                        <h1 class="vancouver-text display-3">
                            <span class="main-colour corner">From Sea to Sky.</span>
                        </h1>
                        <h6 class="vancouver-text mt-2">
                            <span class="main-colour corner">Screenshot by {{$background->credit}}</span>
                        </h6>
                        <h4 class="text-colour mt-3">
                            <span class="corner bg-dark-transparent">
                                <a href="#mid" id="discoverMore" class="blue-text" style="text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;">Come explore Canada's west coast <i class="fas fa-arrow-down ml-2"></i></a>
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4" id="mid">
            <div class="row">
                <!-- First column -->
                <div class="col-md-6">
                    <!-- Recent News -->
                    <div class="card card-background mb-4">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-newspaper mr-2"></i>Recent News</h2>
                        </div>
                        <div class="card-body">
                            @if(count($news) == 0)
                                <h5 class="text-colour text-center">No current News.</h5>
                            @endif
                            @foreach($news as $n)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge text-colour mr-2">{{$n->posted_on_pretty()}}</span>
                                    <a href="{{url('/news').'/'.$n->slug}}" class="text-colour">{{$n->title}}</a>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text text-center">
                            <a href="{{url('/news')}}" class="btn btn-sm btn-link text-secondary px-3"><i class="fas fa-eye mr-2"></i>View all news</a>
                        </div>
                    </div>
                </div>

                <!-- Second column -->
                <div class="col-md-6">
                    <!-- Upcoming Events -->
                    <div class="card card-background mb-4">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-calendar mr-2"></i>Upcoming Events</h2>
                        </div>
                        <div class="card-body">
                            @if(count($nextEvents) == 0)
                                <h5 class="text-colour text-center">Stay tuned here for upcoming events!</h5>
                            @endif
                            @foreach($nextEvents as $e)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{url('/events').'/'.$e->slug}}" class="text-colour">{{$e->name}}</a>
                                    <span class="badge main-colour">{{$e->start_timestamp_pretty()}}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text text-center">
                            <a href="{{url('/events')}}" class="btn btn-sm btn-link text-secondary px-3"><i class="fas fa-eye mr-2"></i>View all events</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- First column -->
                <div class="col-md-6">
                    <!-- Top Controllers this Month -->
                    <div class="card card-background mb-4">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-award mr-2"></i>Top Controllers this Month</h2>
                        </div>
                        <div class="card-body">
                            @if(count($topControllersArray) == 0)
                                <h5 class="text-colour text-center">No data yet.</h5>
                            @endif
                            @foreach($topControllersArray as $t)
                                @if($t['time'] != 0)
                                    <div class="badge badge-light w-100 d-flex justify-content-between align-items-center mb-2" style="background-color: {{$t['colour']}} !important;">
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
                    <div class="card card-background mb-4">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size text-center"><i class="fas fa-user mr-2"></i>Online Controllers</h2>
                        </div>
                        <div class="card-body">
                            @if(count($finalPositions) == 0)
                                <h5 class="text-colour text-center">No controllers online.</h5>
                            @endif
                            @foreach($finalPositions as $p)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="https://stats.vatsim.net/search_id.php?id={{$p->cid}}" target="_blank" class="text-colour">
                                        @if($p->name == $p->cid)
                                            <i class="fas fa-user-circle mr-1"></i>{{$p->name}}
                                        @else
                                            <i class="fas fa-user-circle mr-1"></i>{{$p->name}} {{$p->cid}}
                                        @endif
                                    </a>
                                    <span class="badge main-colour">{{$p->callsign}}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text text-center">
                            <a href="https://map.vatsim.net" target="_blank" class="btn btn-sm btn-link text-secondary px-3"><i class="fas fa-map mr-2"></i>Live VATSIM Map</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-background">
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
            <br>
        </div>
    </div>
@stop