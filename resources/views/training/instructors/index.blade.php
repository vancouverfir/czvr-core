@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@push('styles')
<style>
    .training-instructor-row {
        align-items: center;
        color: var(--czvr-text);
        display: flex;
        gap: 0.8rem;
        padding: 0.85rem 1rem;
        text-decoration: none;
    }

    .training-instructor-row + .training-instructor-row {
        border-top: 1px solid var(--czvr-border);
    }

    .training-instructor-row:hover,
    .training-instructor-row:focus {
        background: var(--czvr-green-soft);
        color: var(--czvr-text);
    }

    .training-instructor-row img {
        border-radius: 50%;
        height: 36px;
        object-fit: cover;
        width: 36px;
    }

    .training-instructor-name {
        flex: 1;
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
    }

    .training-instructor-count {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base);
    }

    .training-assigned-student {
        align-items: center;
        display: flex;
        gap: .75rem;
        padding: .7rem .35rem !important;
        color: var(--czvr-green-dark);
        text-decoration: none;
    }

    .training-assigned-student-name {
        flex: 1;
        font-weight: 500;
        text-decoration: underline;
        text-decoration-color: rgba(54, 122, 37, .35);
        text-underline-offset: 2px;
    }

    .training-assigned-student .fa-chevron-right {
        color: var(--czvr-green);
        font-size: var(--czvr-font-size-base);
        transition: transform .15s ease;
    }

    .training-assigned-student:hover,
    .training-assigned-student:focus {
        background: var(--czvr-green-soft);
        color: var(--czvr-green-dark);
    }

    .training-assigned-student:hover .training-assigned-student-name,
    .training-assigned-student:focus .training-assigned-student-name {
        text-decoration-color: currentColor;
    }

    .training-assigned-student:hover .fa-chevron-right,
    .training-assigned-student:focus .fa-chevron-right {
        transform: translateX(2px);
    }
</style>
@endpush

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
