@extends('layouts.master')
@section('title', 'Event Rosters - Vancouver FIR')
@section('description', 'Vancouver FIR Event Rosters.')

@section('content')
<div class="container py-4">
    <a href="{{ route('dashboard.index') }}" class="blue-text" style="font-size: 1.2em;">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>

    <h1 class="blue-text font-weight-bold mt-2">Event Rosters</h1>
    <hr>

    <div class="row">
        @foreach($events as $e)
            <div class="col-lg-6">
                <div class="card mt-2">
                    <div class="h5 card-header font-weight-bold">
                        {{ $e->name }} - Starting at {{ $e->start_timestamp_pretty() }}
                    </div>
                    <div class="card-body">
                        @if(!isset($e->controllers) || count($e->controllers) === 0)
                            <p>No event roster yet!</p>
                        @else
                            @foreach($e->controllers as $c)
                                <div style="margin-left: 15px; margin-bottom: 5px;">
                                    <span class="font-weight-bold">{{ $c->user->fullName('FLC') }}</span>
                                    is on <span style="color:#007bff;">{{ $c->airport ?? ' ' }}</span>
                                    from {{ ($c->start_timestamp)->format('H:i') }}z
                                    to {{ ($c->end_timestamp)->format('H:i') }}z
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@stop
