@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

@include('includes.trainingMenu')

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <a href="javascript:void(0);" onclick="history.back();" class="blue-text" style="font-size: 1.2em;">
        <i class="fas fa-arrow-left"></i> Student
    </a>

    <hr>

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="font-weight-bold blue-text mb-0">{{ $student->user->fullName('FLC') }}'s Vatcan Notes</h1>
                    <p class="text-muted small mb-0" id="training-notes-count">Total {{ count($vatcanNotes) }}</p>
                </div>
                <div class="small mt-1">Notes left by non Vancouver FIR members not shown!</div>
            </div>
        </div>

        <div class="card-body">
            <div class="list-group" id="training-notes-container">
                @if(count($vatcanNotes) === 0)
                    <div>No Vatcan Notes available! Or you refreshed too many times...</div>
                @else
                    @php
                        $sessionTypes = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];
                        $sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
                        $afacility = ['Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR', 'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'];
                    @endphp

                    @foreach($vatcanNotes as $note)
                        <div class="list-group-item shadow-sm mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="far fa-comment-alt fa-lg text-primary mr-3"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 7px;">
                                            <span class="fw-bold">
                                                {{ $note['instructor_name'] }} - {{ $afacility[$note['facility_id']] ?? 'Unknown' }}
                                            </span>

                                            <span class="text-muted">
                                                {{ \Carbon\Carbon::parse($note['friendly_time'])->format('F j, Y') }}
                                            </span>

                                            <span class="badge bg-primary">
                                                {{ $note['position_trained'] }}
                                            </span>

                                            <span class="badge {{ $sessionBadgeColors[$note['session_type']] ?? 'bg-secondary' }}">
                                                {{ $sessionTypes[$note['session_type']] ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </div>

                                    <span style="white-space: pre-wrap;">{{ $note['training_note'] }}</span>

                                    @if(!empty($note['marking_sheet']))
                                        <hr class="my-3">
                                        <a href="{{ $note['marking_sheet'] }}" target="_blank" class="btn btn-outline-info btn-sm">
                                            <i class="far fa-list-alt me-1"></i> View Marking Sheet
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@stop
