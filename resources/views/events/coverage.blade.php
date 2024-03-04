@extends('layouts.master')
@section('title', 'Event Coverage - Vancouver FIR')
@section('description', 'Need ATC Coverage for an Event?')
@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column justify-content-between align-items-left mb-1">
            <h1 class="blue-text font-weight-bold">Need ATC Coverage for an Event?</h1>
            <h2 class="pb-8">We've Got You.</h2>
        </div>
        <hr>
        
        <div>
            <p>Vancouver is happy to provide ATC for many events within our airspace!</p>
            <p>To request ATC for your event, we recommend contacting Vancouvers's Events Coordinator by submitting a <a href="{{route('tickets.index')}}">ticket</a> or <a href="{{route('staff')}}">email</a> If the position is vacant, instead contact the FIR Chief.</p>
            <p>Thank you for choosing Vancouver!</p>
        </div>
        <div class="pt-4">
            <h3>For specifics about flying into and out of our airspace during an event please see: <a href="https://czvr.ca/storage/files/uploads/1709591072.pdf">CZVR Streamer and Event Guide</a></h3>
        </div>
    </div>
@endsection
