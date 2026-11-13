@extends('layouts.master')
@section('title', 'Events - Vancouver FIR')
@section('description', 'Check out the Vancouver FIR events!')

@push('styles')
    <link href="{{ asset('css/pages/events.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        function openCoverageFromHash() {
            if (window.location.hash !== '#eventCoverage') {
                return;
            }

            const coverage = document.getElementById('eventCoverage');

            if (coverage && window.bootstrap) {
                bootstrap.Collapse.getOrCreateInstance(coverage, { toggle: false }).show();
                coverage.scrollIntoView({ block: 'start' });
            }
        }

        document.addEventListener('DOMContentLoaded', openCoverageFromHash);
        window.addEventListener('hashchange', openCoverageFromHash);
    </script>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-row justify-content-between align-items-center mb-1">
            <h1 class="blue-text fw-bold">Events</h1>
            <a href="#eventCoverage" class="event-coverage-toggle" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="eventCoverage">
                Need ATC Coverage? <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </a>
        </div>
        <hr>

        <section class="collapse event-coverage-panel" id="eventCoverage">
            <div class="event-coverage-content">
                <div class="event-coverage-copy">
                    <h2>Bring your event through Vancouver!</h2>
                    <p>Planning an event through Vancouver airspace? We’re happy to help! Send the details to Vancouver’s Events Coordinator, or the FIR Chief if that position is vacant!</p>
                </div>
                <div class="event-coverage-actions">
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-success">Submit a ticket</a>
                    <a href="{{ route('staff') }}" class="btn btn-sm btn-success">Staff contacts</a>
                    <a href="https://czvr.ca/storage/files/fir-streamer-event-document.pdf" target="_blank" rel="noopener" class="event-guide-link">
                        Event/Streamer Guide <i class="fas fa-arrow-up-right-from-square fa-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <ul class="list-unstyled">
            @if (count($events) == 0)
                <li>No Events Stay tuned!</li>
            @endif
            @foreach($events as $e)
                <div class="card my-2" style="@if($e->image_url) background-image:url({{ $e->image_url }}); background-size: cover; background-position: center; color: white; @endif">
                    <div class="event-card-content" style="@if($e->image_url) background-color: rgba(0, 0, 0, 0.6); @endif border-radius: inherit;">
                        <div class="p-3">
                            <a href="{{ route('events.view', $e->slug) }}">
                                <h3 style="@if($e->image_url) color: white @else color: var(--czvr-green-dark) @endif">{{ $e->name }}</h3>
                            </a>
                            <h5>{{ $e->start_timestamp_pretty() }} to {{ $e->end_timestamp_pretty() }}</h5>
                            @if ($e->departure_icao && $e->arrival_icao)
                                <h5 class="fw-bold">{{ $e->departure_icao }}&nbsp;&nbsp;<i class="fas fa-plane"></i>&nbsp;&nbsp;{{ $e->arrival_icao }}</h5>
                            @endif
                            @if (!$e->event_in_past())
                                Starts {{ $e->starts_in_pretty() }}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </ul>
    </div>
@endsection
