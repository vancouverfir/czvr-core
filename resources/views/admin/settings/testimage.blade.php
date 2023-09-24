@extends('layouts.master')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />

    <div class="winnipeg-blue">
        <div data-jarallax data-speed="0.2" class="jarallax" style="height: min(calc(100vh - 59px), 1080px)">
            <div class="mask flex-center flex-column"
                 style="z-index: 1; width: 100%; background-image: url({{$image->url}}); {{$image->css}}">
                <div class="container" style="padding-bottom: 20em">
                    <div class="py-5">
                        <div>
                            <br>
                            <h1 class="vancouver-text" style="font-size: 5em">
                                <span class="main-colour corner" style="padding: 1%">We Are Vancouver.</span>
                            </h1>
                            <h6 class="vancouver-text" style="font-size: 1.25em;">
                                <span class="main-colour corner" style="padding: 0.5%">Screenshot by {{$image->credit}}</span>
                            </h6>
                            <br>
                            <h4 class="text-colour" style="font-size: 2em;">
                                <span class="corner" style="padding: 0.5%; background-color: #00000066"><a href="#mid" id="discoverMore" class="blue-text">Come explore the left of Canada.&nbsp;&nbsp;<i class="fas fa-arrow-down"></i></a></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop
