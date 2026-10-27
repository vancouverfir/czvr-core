@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
    @include('includes.trainingMenu')

    <main class="container training-page">
        <header class="training-page-header">
            <div>
                <h1 class="blue-text">Instructors</h1>
                <p>Review active instructors and their assigned students!</p>
            </div>
            <span class="text-muted small">{{ $instructors->count() }} active</span>
        </header>

        <div class="training-surface">
            @forelse($instructors as $instructor)
                <a href="#" data-bs-toggle="modal" data-bs-target="#students{{ $instructor->id }}" class="training-instructor-row">
                    <img src="{{ $instructor->user->avatar() }}" alt="">
                    <span class="training-instructor-name">{{ $instructor->user->fullName('FLC') }}</span>
                    <span class="training-instructor-count">
                        @if($instructor->id == 1)
                            System account
                        @else
                            {{ $instructor->students->count() }} {{ Str::plural('student', $instructor->students->count()) }}
                        @endif
                    </span>
                    <i class="fas fa-chevron-right text-muted" aria-hidden="true"></i>
                </a>

                <div class="modal fade" id="students{{ $instructor->id }}" tabindex="-1" aria-labelledby="students{{ $instructor->id }}Title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="students{{ $instructor->id }}Title">{{ $instructor->user->fullName('FLC') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if($instructor->id == 1)
                                    <p class="text-muted mb-0">This system account identifies automatic training actions.</p>
                                @else
                                    <div class="list-group list-group-flush">
                                        @forelse ($instructor->students as $assignedStudent)
                                            <a href="{{ route('training.students.view', $assignedStudent->id) }}" class="list-group-item list-group-item-action training-assigned-student">
                                                <span class="training-assigned-student-name">
                                                    {{ $assignedStudent->user->fullName('FLC') }}
                                                    <small class="text-muted ms-1">{{ $assignedStudent->user->rating_short }}</small>
                                                </span>
                                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                                <span class="visually-hidden">View student</span>
                                            </a>
                                        @empty
                                            <p class="text-muted mb-0">No students assigned.</p>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted">No active instructors found!</div>
            @endforelse
        </div>
    </main>
@stop
