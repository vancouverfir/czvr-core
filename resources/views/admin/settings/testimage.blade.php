@extends('layouts.master')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />

    <div class="winnipeg-blue">
        <div data-jarallax data-speed="0.2" class="jarallax" style="height: min(calc(100vh - 59px), 1080px)">
        <div class="mask"
                style="z-index: -1; width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; background: url({{$image->url}}); background-size: cover; background-position: center; animation: heroZoom 10s ease-in-out infinite alternate;">
            </div>

            <style>
                @keyframes heroZoom {0% { transform: scale(1); }100% { transform: scale(1.03); }}
            </style>

            <div class="container" style="text-align: center; padding-top: 30px; padding-bottom: 30px;">
                <div style="display: inline-block; position: relative; z-index: 1; padding: 30px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <h1 class="vancouver-text display-3" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); color: #fff;">
                        <span class="corner">From Sea to Sky!</span>
                    </h1>
                    <h4 class="vancouver-text mt-2" style="text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);">
                        <a href="#mid" id="discoverMore" class="blue-text" style="color: #fff; text-decoration: none;">Come explore Canada's West Coast<i class="fas fa-arrow-down ml-2"></i></a>
                    </h4>
                    <small style="color: #fff; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">
                        <i>Screenshot by {{$image->credit}}</i>
                    </small>
                </div>
            </div>
    </div>
        </div>
@stop
