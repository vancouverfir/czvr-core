@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@push('styles')
<link href="{{ asset('css/pages/training-availability.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/training-student-dashboard.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/training-instructor-dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Instructors -->
    @if (! $preview && auth()->check() && (auth()->user()->activeInstructorProfile !== null || auth()->user()->can('training.manage')))
    @include('includes.trainingMenu')
    <main class="container py-4 training-portal">
        <header class="training-portal-header">
            <div>
                <h1 class="blue-text">Training Centre</h1>
                <p>Your students and upcoming sessions are together in one place!</p>
            </div>
            <a href="{{ route('training.instructingsessions.index', ['action' => 'new']) }}" class="btn btn-primary btn-sm flex-shrink-0">
                New session
            </a>
        </header>

        <div class="training-portal-grid">
            <section class="training-panel" aria-labelledby="assigned-students-heading">
                <div class="training-panel-header">
                    <h2 id="assigned-students-heading">Your Students</h2>
                    <span class="training-panel-count">{{ $yourStudents->count() }} assigned</span>
                </div>

                @forelse ($yourStudents as $assignedStudent)
                    @php $nextSession = $assignedStudent->instructingSessions->first(); @endphp
                    <a href="{{ route('training.students.view', $assignedStudent->id) }}" class="training-student-row">
                        <img src="{{ $assignedStudent->user->avatar() }}" class="training-student-avatar" alt="">
                        <span class="training-row-copy">
                            <span class="training-row-title">{{ $assignedStudent->user->fullName('FLC') }}</span>
                            <span class="training-row-meta">
                                @if ($nextSession)
                                    {{ $nextSession->start_time->isToday()
                                        ? 'Next session today at '.$nextSession->start_time->format('H:i').' UTC'
                                        : 'Next session '.$nextSession->start_time->format('d M Y, H:i').' UTC' }}
                                @else
                                    No upcoming session
                                @endif
                            </span>
                        </span>
                        <span class="training-status {{ $assignedStudent->status == \App\Models\AtcTraining\Student::STATUS_MARKED_FOR_REMOVAL ? 'training-status-hold' : ($assignedStudent->isConsolidating() ? 'training-status-consolidating' : 'training-status-open') }}">
                            {{ $assignedStudent->status == \App\Models\AtcTraining\Student::STATUS_MARKED_FOR_REMOVAL ? 'On hold' : ($assignedStudent->isConsolidating() ? number_format($assignedStudent->consolidationRemainingHours(), 1).'h remaining' : 'Ready') }}
                        </span>
                    </a>
                @empty
                    <div class="training-empty">No students are currently allocated to you.</div>
                @endforelse
            </section>

            <section class="training-panel" aria-labelledby="upcoming-sessions-heading">
                <div class="training-panel-header">
                    <h2 id="upcoming-sessions-heading">Upcoming Sessions</h2>
                    <a href="{{ route('training.instructingsessions.index') }}" class="training-panel-count">View calendar</a>
                </div>

                @forelse ($upcomingSessions as $session)
                    <a href="{{ route('training.instructingsessions.index', ['session' => $session->id]) }}" class="training-session-row">
                        <span class="training-session-date">
                            <span>{{ $session->start_time->format('M') }}</span>
                            <strong>{{ $session->start_time->format('d') }}</strong>
                        </span>
                        <span class="training-row-copy">
                            <span class="training-row-title">{{ $session->student->user->fullName('FLC') }}</span>
                            <span class="training-row-meta">{{ $session->start_time->format('H:i') }}–{{ $session->end_time->format('H:i') }} UTC · {{ $session->title }}</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="training-empty">No upcoming sessions scheduled.</div>
                @endforelse
            </section>
        </div>
    </main>
    @elseif ($student)

    <!-- Student -->
    @include('includes.trainingMenu')
    @php
        $hasAvailabilityErrors = $errors->has('availability')
            || $errors->has('availability.*')
            || $errors->has('availability_comment');
        $studentStatus = match ($student->status) {
            0 => $waitlistPosition ? 'Waitlist position '.$waitlistPosition : 'Waiting for placement',
            3 => 'Visiting-controller waitlist',
            4 => 'Training on hold',
            9 => 'Training completed',
            default => 'Training in progress',
        };
    @endphp

    <main class="container training-page training-student-dashboard">
        @if ($preview)
            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span><i class="far fa-eye me-1" aria-hidden="true"></i>You are previewing what <strong>{{ $student->user->fullName('FLC') }}</strong> sees! Nothing on this page can be changed.</span>
                <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-sm btn-light">Back to student</a>
            </div>
        @endif
        <header class="training-page-header">
            <div>
                <h1 class="blue-text">My Training</h1>
                <p>Your progress, availability, and latest training notes.</p>
            </div>
        </header>

        <section class="student-dashboard-identity" aria-label="Training overview">
            <img src="{{ $student->user->avatar() }}" class="student-dashboard-avatar" alt="">
            <div class="student-dashboard-person">
                <h2>{{ $student->user->fullName('FLC') }}</h2>
                <p>{{ $student->user->rating_short }} · {{ $student->user->homeFacilityName() }}</p>
                @if ($student->labels->isNotEmpty())
                    <div class="student-dashboard-labels">
                        @foreach ($student->labels as $label)
                            <span class="student-dashboard-label" style="background-color: {{ $label->label->color }};">
                                @if ($label->label->fa_icon)<i class="{{ $label->label->fa_icon }} fa-fw me-1" aria-hidden="true"></i>@endif{{ $label->label->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="student-dashboard-facts">
                <div class="student-dashboard-fact">
                    <span class="student-dashboard-fact-label">Instructor</span>
                    <span class="student-dashboard-fact-value">
                        @if ($student->instructor)
                            <img src="{{ $student->instructor->user->avatar() }}" alt="">
                            {{ $student->instructor->user->fullName('FL') }}
                        @else
                            <i class="far fa-user" aria-hidden="true"></i>Not assigned
                        @endif
                    </span>
                </div>
                <div class="student-dashboard-fact">
                    <span class="student-dashboard-fact-label">Status</span>
                    <span class="student-dashboard-fact-value"><i class="far fa-compass" aria-hidden="true"></i>{{ $studentStatus }}</span>
                </div>
                @if ($student->status === \App\Models\AtcTraining\Student::STATUS_WAITLIST)
                    <div class="student-dashboard-fact">
                        <span class="student-dashboard-fact-label">Current wait for new students</span>
                        <span class="student-dashboard-fact-value">{{ $training_time?->wait_length ?? 'Not currently available' }}</span>
                    </div>
                @endif
            </div>
        </section>

        @if ($student->renewalWindowExpired())
            <div class="student-dashboard-notice student-dashboard-notice-danger">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                <span>Your renewal window has expired and the waitlist status is being updated.</span>
            </div>
        @elseif ($student->hasPendingRenewal())
            <div class="student-dashboard-notice student-dashboard-notice-warning">
                <i class="fas fa-envelope-open-text" aria-hidden="true"></i>
                <div>
                    <strong>Monthly renewal required.</strong>
                    Confirm your availability to keep your current waitlist position.
                    @unless ($preview)
                        <a href="{{ route('training.renew', $student->renewal_token) }}" class="ms-1">Review renewal</a>
                    @endunless
                </div>
            </div>
        @endif

        @if ($student->consolidationIsPending())
            <div class="student-dashboard-notice">
                <i class="far fa-hourglass-half" aria-hidden="true"></i>
                <span><strong>{{ number_format($student->consolidationHours(), 1) }} / {{ number_format(\App\Services\TrainingProgressionService::CONSOLIDATION_HOURS, 0) }} hours</strong> logged at {{ $student->consolidation_rating }}. Training unlocks automatically when consolidation is complete.</span>
            </div>
        @endif

        <div class="student-dashboard-grid {{ $studentChecklistGroups?->isEmpty() ? 'student-dashboard-grid-single' : '' }}">
            @if ($studentChecklistGroups?->isNotEmpty())
                <section class="student-dashboard-panel" aria-labelledby="studentChecklistHeading">
                    <div class="student-dashboard-panel-header">
                        <div>
                            <h2 id="studentChecklistHeading">Your Checklists</h2>
                            <p>Training items and current completion status</p>
                        </div>
                        @if ($studentChecklistGroups->flatten(1)->contains(fn ($item) => $item->completed))
                            <button id="toggle-completed" class="btn btn-sm btn-outline-primary">Hide completed</button>
                        @endif
                    </div>
                    <div class="student-dashboard-panel-body">
                        @foreach ($studentChecklistGroups as $checklistName => $items)
                            <div class="student-checklist-group">
                                <h3>{{ $checklistName }}</h3>
                                @foreach ($items as $item)
                                    <div class="student-checklist-item {{ $item->completed ? 'completed-row' : '' }}">
                                        <i class="{{ $item->completed ? 'fas fa-check-circle' : 'far fa-circle' }}" aria-hidden="true"></i>
                                        <span>{{ $item->checklistItem->item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="student-dashboard-panel" aria-labelledby="studentAvailabilityHeading">
                <div class="student-dashboard-panel-header">
                    <div>
                        <h2 id="studentAvailabilityHeading">Availability</h2>
                    </div>
                    @if (filled($student->availability) && ! $preview)
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#availabilityModal">
                            <i class="fas fa-pen me-1" aria-hidden="true"></i>Edit
                        </button>
                    @endif
                </div>
                <div class="student-dashboard-panel-body">
                    @if (filled($student->availability))
                        <div class="student-availability-schedule">
                            @foreach ($student->availabilityPeriods() as $period)
                                <div class="student-availability-day">
                                    <span class="student-availability-day-name">{{ $period['days'] }}</span>
                                    <div class="student-availability-periods">
                                        <span class="student-availability-period">
                                            <time datetime="{{ $period['start'] }}">{{ $period['start'] }}</time>
                                            <span aria-hidden="true">–</span>
                                            <time datetime="{{ $period['end'] }}">{{ $period['end'] }}</time>
                                            <small>UTC</small>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($student->times)
                            <div class="student-availability-note">
                                <i class="far fa-comment" aria-hidden="true"></i>
                                <div>
                                    <span>Note for your instructor</span>
                                    <p>{{ $student->times }}</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($preview)
                        <div class="student-availability-empty text-muted">No availability submitted yet!</div>
                    @else
                        <div class="student-availability-empty">
                            <button type="button" class="btn btn-primary student-availability-submit" data-bs-toggle="modal" data-bs-target="#availabilityModal">
                                Submit availability
                            </button>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        @unless ($preview)
        <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalTitle" aria-hidden="true" @if($hasAvailabilityErrors) data-open-on-load="true" @endif>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('training.students.editTimes', $student->id) }}">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h2 class="modal-title h5" id="availabilityModalTitle">Training availability</h2>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('training.students.partials.availability-fields', ['availabilityEditorId' => 'portalAvailability'])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary">Save availability</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endunless

        @if (! empty($vatcanNotes))
            <section class="student-dashboard-panel" aria-labelledby="studentNotesHeading">
                <div class="student-dashboard-panel-header">
                    <div>
                        <h2 id="studentNotesHeading">Latest VATCAN Notes</h2>
                        <p>Latest three notes from Vancouver FIR</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#allVatcanNotes">View all</button>
                </div>
                <div class="student-dashboard-panel-body">
                    @foreach (array_slice($vatcanNotes, 0, 3) as $note)
                        @include('training.students.partials.vatcan-note', ['note' => $note])
                    @endforeach
                </div>
            </section>
            @include('training.students.partials.vatcan-notes-modal')
        @endif
    </main>

    <script src="{{ asset('js/training-availability.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const availabilityModal = document.querySelector('#availabilityModal[data-open-on-load="true"]');
            if (availabilityModal && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(availabilityModal).show();
            }

            const completedButton = document.getElementById('toggle-completed');
            let completedHidden = false;

            completedButton?.addEventListener('click', () => {
                document.querySelectorAll('.completed-row').forEach((row) => {
                    row.hidden = !completedHidden;
                });
                completedHidden = !completedHidden;
                completedButton.textContent = completedHidden ? 'Show completed' : 'Hide completed';
            });
        });
    </script>

    @else
        <div class="d-flex justify-content-center align-items-center min-vh-100" style="margin-top: 50px; margin-bottom: 50px;">
            <div class="text-center" style="max-width: 700px; width: 100%;">
                <h1 class="fw-bold blue-text mb-3">
                    <i class="fas fa-info-circle"></i> Uh Oh! 👀
                </h1>

                <p style="font-size: 1.2em; margin-bottom: 1.5rem;">
                    You’ve wandered into a page reserved for home and visiting controllers only!<br><br>
                    Don’t worry — here are some useful links to get you back on track!
                </p>

                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <a href="{{ route('trainingtimes') }}" class="btn btn-outline-primary">
                        <i class="fas fa-book"></i> Training FAQ
                    </a>
                    <a href="{{ route('join.public') }}" class="btn btn-outline-success">
                        <i class="fas fa-plane-departure"></i> Join Our FIR
                    </a>
                    <a href="{{ route('staff') }}" class="btn btn-outline-info">
                        <i class="fas fa-envelope"></i> Contact Staff
                    </a>
                </div>
            </div>
        </div>
    @endif

@stop
