@extends('layouts.master')
@section('description', 'Welcome to Vanouver - located in the left of Canada on the VATSIM network.')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
    <div>
        <div data-jarallax data-speed="0.2" class="jarallax" style="height: min(calc(100vh - 59px), 1080px)">
            <div class="mask flex-center flex-column"
                 style="z-index: 1; width: 100%; background-image: url({{$background->url}}); {{$background->css}}">
                <div class="container" style="padding-bottom: 20em">
                    <div class="py-5">
                        <div>
                            <br>
                            <h1 class="vancouver-text" style="font-size: 5em">
                                <span class="main-colour corner" style="padding: 1%">From Sea to Sky.</span>
                            </h1>
                            <h6 class="vancouver-text" style="font-size: 1.25em;">
                                <span class="main-colour corner" style="padding: 0.5%">Screenshot by {{$background->credit}}</span>
                            </h6>
                            <br>
                            <h4 class="text-colour" style="font-size: 2em;">
                                <span class="corner" style="padding: 0.5%; background-color: #00000066"><a href="#mid" id="discoverMore" class="blue-text">Come explore Canada's west coast&nbsp;&nbsp;<i class="fas fa-arrow-down"></i></a></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="mid">
            <div class="row">
                <div class="col-md-6 card-padding">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size" style="text-align: center; padding-top:1%"><i class="fas fa-newspaper"></i>&nbsp;&nbsp;Recent News</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($news) == 0)
                            <h5 class="text-colour" style="text-align: center;">No current News.</h5>
                            @endif
                            @foreach($news as $n)
                                <h5><span class="badge text-colour">{{$n->posted_on_pretty()}}</span>&nbsp;&nbsp;<a href="{{url('/news').'/'.$n->slug}}" class="text-colour"><text class="align-middle">{{$n->title}}</text></h5></a>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text">
                            <a href="{{url('/news')}}"><h6 style="text-align: center;"><i class="fas fa-eye"></i>&nbsp;View all news</h6></a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-6 card-padding">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size" style="text-align: center; padding-top:1%"><i class="fas fa-calendar"></i>&nbsp;&nbsp;Upcoming Events</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($nextEvents) == 0)
                                <h5 class="text-colour" style="text-align: center;">Stay tuned here for upcoming events!</h5>
                            @endif
                            @foreach($nextEvents as $e)
                                <h5 class="text-colour"><a href="{{url('/events').'/'.$e->slug}}" class="text-colour"><text class="align-middle">{{$e->name}}</text></a>&nbsp;&nbsp;<span class="float-right badge main-colour">{{$e->start_timestamp_pretty()}}</span></h5>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text">
                            <a href="{{url('/events')}}"><h6 style="text-align: center;"><i class="fas fa-eye"></i>&nbsp;View all events</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style=" min-height: 100%">
                <div class="col-md-6 card-padding">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size" style="text-align: center; padding-top:1%"><i class="fas fa-award"></i>&nbsp;&nbsp;Top Controllers this Month</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($topControllersArray) == 0)
                                <h5 class="text-colour" style="text-align: center;">No data yet.</h5>
                            @endif
                            @foreach($topControllersArray as $t)
                                @if($t['time'] != 0)
                                    <h2>
                                        <span class="badge badge-light w-100" style="background-color: {{$t['colour']}} !important;">
                                            <div style="float: left;">
                                                {{User::where('id', $t['cid'])->first()->fullName('FLC')}}
                                            </div>
                                            <div style="float: right;">
                                                {{$t['time']}}
                                            </div>
                                        </span>
                                    </h2>
                                @endif
                            @endforeach
                        </div>
                        <div class="card-footer">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 card-padding">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size" style="text-align: center; padding-top:1%"><i class="fas fa-user"></i>&nbsp;&nbsp;Online Controllers</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($finalPositions) == 0)
                                <h5 class="text-colour" style="text-align: center;">No controllers online.</h5>
                            @endif
                            @foreach($finalPositions as $p)
                                <h5 class="text-colour">
                                    <div style="float: left;">
                                        <a href="https://stats.vatsim.net/search_id.php?id={{$p->cid}}" target="_blank" class="text-colour">
                                            @if($p->name == $p->cid)
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}}
                                            @else
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}} {{$p->cid}}
                                            @endif
                                        </a>
                                    </div>
                                    <div style="float: right;">
                                    <span class="badge main-colour">
                                        {{$p->callsign}} on {{$p->frequency}}
                                    </span>
                                    </div>
                                </h5>
                                <br>
                            @endforeach
                        </div>
                        <div class="card-footer card-hf-padding blue-text">
                            <a href="https://map.vatsim.net" target="_blank"><h6 style="text-align: center;"><i class="fas fa-map"></i>&nbsp;Live VATSIM Map</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 card-padding">
                    <div class="card card-background" style="width: 100%">
                        <div class="card-header card-hf-padding blue-text">
                            <h2 class="font-weight-bold card-header-size" style="text-align: center; padding-top:1%"><i class="fas fa-sun"></i>&nbsp;&nbsp;Weather</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:0%">
                        @if(count($weather) == 0)
                                <h5 class="text-colour" style="text-align: center;">No weather data.</h5>
                                @endif
                            <div class="text-colour" style="float: left;">
                                @foreach($weather as $w)
                                    <h5 class="text-colour"><text class="align-middle font-weight-bold">{{$w->icao}} - {{$w->station->name}}&nbsp;&nbsp;</text>
                                        <span class="badge {{$w->flight_category}}">{{$w->flight_category}}</span>
                                    @if(Carbon\Carbon::make($w->observed) < Carbon\Carbon::now()->subHours(2))
                                        <span class="badge grey">OUTDATED</span>
                                    @endif
                                    </h5>
                                    {{$w->raw_text}}
                                    <br><br>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                        </div>
                    </div>
                </div>
            </div>

            <br>
        </div>
    </div>
@stop
