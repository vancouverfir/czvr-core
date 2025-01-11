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
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('js/livemap.js') }}"></script>

@endsection
