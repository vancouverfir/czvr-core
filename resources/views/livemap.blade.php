@extends('layouts.master')

@section('title', 'Live Map - Vancouver FIR')

@section('content')

<div class="container py-4">

    <h1 class="font-weight-bold blue-text" style="font-size: 24px;"><strong>Available Gates</strong></h1>

    <!-- Map -->
    <div id="last-updated">Last Updated: </div>
    <div class="row">
        <div class="col-md-12">
            <div id="map" style="height: 600px;"></div>
        </div>
    </div>
    
    <hr>
    
    <!-- Online Controllers -->
    <div class="row">
        <div class="col-md-6 d-flex flex-column">
            <div class="card mb-2 flex-grow-1" style="border-width: 1px; border-style: solid; border-color: #302c2c;">
                <div class="card-header card-hf-padding blue-text">
                    <h2 class="font-weight-bold card-header-size" style="text-align: center; font-size: 18px;">Online Controllers</h2>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <h5 class="text-colour" style="text-align: center;">No controllers online.</h5>
                </div>
            </div>
        </div>
        
        <!-- Atis/Metar -->
        <div class="col-md-6 d-flex flex-column">
            <div class="card mb-2 flex-grow-1" style="border-width: 1px; border-style: solid; border-color: #302c2c;">
                <div class="card-header card-hf-padding">
                    <h2 class="font-weight-bold card-header-size" style="text-align: center; color: #6cc249; font-size: 18px;">Vancouver ATIS/METAR</h2>
                </div>
                <div class="card-body">
                    {{\App\Classes\WeatherHelper::getAtis('CYVR')}}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('js/livemap.js') }}"></script>

@endsection
