@extends('layouts.master')
@section('title', 'Events - Vancouver FIR')
@section('description', 'Check out the Vancouver FIR events!')
@section('content')
    <div class="container py-4">
        <div class="d-flex flex-row justify-content-between align-items-center mb-1">
            <h1 class="blue-text font-weight-bold">Upcoming Events</h1>
            <a href="{{route('events.coverage')}}" class="btn btn-link float-right mx-0 px-0 content-font-color">Need ATC Coverage? Click Here!</a>
        </div>
        <hr>
        <ul class="list-unstyled">
            @if (count($events) == 0)
            <li>No Events... Stay tuned!</li>
            @endif
            @foreach($events as $e)
            <div class="card my-2" style="@if($e->image_url) background-image:url({{$e->image_url}}); background-size: cover; background-position: center; color: white; @endif">
                <div class="card" style="@if($e->image_url) background-color: rgb(0, 0, 0, 0.6) @endif">
                    <div class="p-3">
                        <a href="{{route('events.view', $e->slug)}}">
                            <h3 style="@if($e->image_url) color: white @else color: #013162 @endif">{{$e->name}}</h3>
                        </a>
                        <h5>{{$e->start_timestamp_pretty()}} to {{$e->end_timestamp_pretty()}}</h5>
                        @if ($e->departure_icao && $e->arrival_icao)
                            <h5 class="font-weight-bold">{{$e->departure_icao}}&nbsp;&nbsp;<i class="fas fa-plane"></i>&nbsp;&nbsp;{{$e->arrival_icao}}</h5>
                        @endif
                        @if (!$e->event_in_past())
                        Starts {{$e->starts_in_pretty()}}
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </ul>
    </div>
@endsection
