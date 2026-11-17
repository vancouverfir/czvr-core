@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

@php
    $sessionPayload = $upcomingSessions->map(fn ($session) => [
        'id' => $session->id,
        'title' => $session->title,
        'studentId' => $session->student_id,
        'student' => $session->student?->user?->fullName('FLC'),
        'instructor' => $session->instructorUser()?->fullName('FLC'),
        'comment' => $session->instructor_comment,
        'start' => $session->start_time->utc()->format('Y-m-d\TH:i:s\Z'),
        'end' => $session->end_time->utc()->format('Y-m-d\TH:i:s\Z'),
        'updateUrl' => route('training.instructingsessions.update', $session),
        'cancelUrl' => route('training.instructingsessions.cancel', $session),
    ])->values();
    $oldSessionValues = $errors->any() ? [
        'student_id' => old('student_id'),
        'title' => old('title'),
        'start_time' => old('start_time'),
        'end_time' => old('end_time'),
        'instructor_comment' => old('instructor_comment'),
    ] : null;

    $now = now('UTC');
    $onNow = $upcomingSessions->filter(fn ($session) => $session->start_time->lte($now))->values();
    $nextSession = $upcomingSessions->first(fn ($session) => $session->start_time->gt($now));
    $sessionsByDay = $upcomingSessions->groupBy(fn ($session) => $session->start_time->copy()->utc()->max($now)->format('Y-m-d'));
    $dayName = function ($date) use ($now) {
        $day = \Carbon\Carbon::parse($date, 'UTC');

        return $day->isSameDay($now) ? 'Today' : ($day->isSameDay($now->copy()->addDay()) ? 'Tomorrow' : $day->format('D j M'));
    };
@endphp

<main class="container training-page booking-page">
    <header class="training-page-header">
        <div>
            <h1 class="blue-text">Instructing Sessions</h1>
            <p>All times are UTC!</p>
        </div>
        <div class="booking-header-side">
            <span class="booking-clock" aria-label="Current time in UTC"><span id="utcClock">--:--:--</span> <small>UTC</small></span>
            <button type="button" id="newSessionButton" class="btn btn-sm btn-primary flex-shrink-0"><i class="fas fa-plus me-1"></i>New session</button>
        </div>
    </header>

    <div class="booking-strip">
        <span class="booking-strip-part">
            <span class="booking-strip-label">Happening now</span>
            @forelse ($onNow as $session)
                <span><strong>{{ $session->title }}</strong> · {{ $session->student?->user?->fullName('FL') }} · until {{ $session->end_time->copy()->utc()->format('H:i') }} UTC</span>
            @empty
                <span>No sessions right now</span>
            @endforelse
        </span>
        @if ($nextSession)
            <span class="booking-strip-part">
                <span class="booking-strip-label">Next</span>
                <span><strong>{{ $nextSession->title }}</strong> · {{ $nextSession->start_time->copy()->utc()->isSameDay($now) ? '' : $nextSession->start_time->copy()->utc()->format('D j M').', ' }}{{ $nextSession->start_time->copy()->utc()->format('H:i') }} UTC</span>
            </span>
        @endif
    </div>

    <section class="training-panel" aria-labelledby="upcoming-sessions-heading">
        <div class="training-panel-header">
            <h2 id="upcoming-sessions-heading">Upcoming Sessions</h2>
        </div>

        @forelse ($sessionsByDay as $date => $sessions)
            <div class="booking-day-label">{{ $dayName($date) }}</div>
            @foreach ($sessions as $session)
                @php
                    $sessionStart = $session->start_time->copy()->utc();
                    $sessionEnd = $session->end_time->copy()->utc();
                    $isMine = $session->instructorUser()?->id === auth()->id();
                @endphp
                <button type="button" class="training-session-row booking-row-button" data-session-id="{{ $session->id }}">
                    <span class="training-session-date">
                        <span>{{ $sessionStart->format('M') }}</span>
                        <strong>{{ $sessionStart->format('d') }}</strong>
                    </span>
                    <span class="training-row-copy">
                        <span class="training-row-title">{{ $session->title }}</span>
                        <span class="training-row-meta">{{ $sessionStart->format('H:i') }}–{{ $sessionEnd->format('H:i') }} UTC · {{ $session->student?->user?->fullName('FL') }} with {{ $session->instructorUser()?->fullName('FL') }}</span>
                    </span>
                    @if ($sessionStart->lte($now))
                        <span class="training-status training-status-open">Happening now</span>
                    @elseif ($isMine)
                        <span class="training-status booking-status-mine">Yours</span>
                    @endif
                    <i class="fas fa-chevron-right booking-row-chevron" aria-hidden="true"></i>
                </button>
            @endforeach
        @empty
            <div class="training-empty">No upcoming sessions!</div>
        @endforelse
    </section>
</main>

<div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="sessionModalTitle">Session</h5>
                    <small class="text-muted" id="sessionModalSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="sessionSummary" hidden>
                    <div class="session-summary-grid">
                        <div class="session-summary-item"><span>Student</span><strong id="summaryStudent"></strong></div>
                        <div class="session-summary-item"><span>Instructor</span><strong id="summaryInstructor"></strong></div>
                        <div class="session-summary-item"><span>Starts</span><strong id="summaryStart"></strong></div>
                        <div class="session-summary-item"><span>Ends</span><strong id="summaryEnd"></strong></div>
                    </div>
                    <div id="summaryComment" class="session-comment" hidden></div>
                </div>

                <form id="sessionForm" method="POST" action="{{ route('training.instructingsessions.create') }}" hidden>
                    @csrf
                    <input type="hidden" name="_method" id="sessionMethod" value="PUT" disabled>
                    <input type="hidden" name="session_form_mode" id="sessionFormMode" value="create">

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Instructor</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->fullName('FLC') }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="sessionStudent" class="form-label">Student</label>
                        <select name="student_id" id="sessionStudent" class="form-select" required data-searchable>
                            <option value="">Select a student</option>
                            @foreach ($students as $student)
                                @if ($student->isConsolidating())
                                    <option value="{{ $student->id }}" disabled>
                                        {{ $student->user->fullName('FLC') }} — {{ number_format($student->consolidationRemainingHours(), 1) }} consolidation hours remaining
                                    </option>
                                @else
                                    <option value="{{ $student->id }}">
                                        {{ $student->user->fullName('FLC') }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sessionTitle" class="form-label">Title</label>
                        <input type="text" name="title" id="sessionTitle" class="form-control" maxlength="255" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="sessionStart" class="form-label">Start time UTC</label>
                            <input type="datetime-local" name="start_time" id="sessionStart" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sessionEnd" class="form-label">End time UTC</label>
                            <input type="datetime-local" name="end_time" id="sessionEnd" class="form-control" required>
                        </div>
                    </div>

                    <div>
                        <label for="sessionComment" class="form-label">Instructor comment <span class="text-muted">(optional)</span></label>
                        <textarea name="instructor_comment" id="sessionComment" class="form-control" rows="3" maxlength="5000"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <form id="deleteSessionForm" method="POST" class="me-auto" hidden onsubmit="return confirm('Cancel this instructing session?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel session</button>
                </form>
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="editSessionButton" class="btn btn-sm btn-outline-primary" hidden>Edit</button>
                <button type="submit" id="saveSessionButton" class="btn btn-sm btn-success" form="sessionForm" hidden>Save session</button>
            </div>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sessions = @json($sessionPayload);
    const sessionsById = new Map(sessions.map((session) => [String(session.id), session]));
    const modalElement = document.getElementById('sessionModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const form = document.getElementById('sessionForm');
    const method = document.getElementById('sessionMethod');
    const formMode = document.getElementById('sessionFormMode');
    const student = document.getElementById('sessionStudent');
    const title = document.getElementById('sessionTitle');
    const start = document.getElementById('sessionStart');
    const end = document.getElementById('sessionEnd');
    const comment = document.getElementById('sessionComment');
    const summary = document.getElementById('sessionSummary');
    const editButton = document.getElementById('editSessionButton');
    const saveButton = document.getElementById('saveSessionButton');
    const deleteForm = document.getElementById('deleteSessionForm');
    let currentSession = null;

    const setStudent = (value) => $(student).val(value ? String(value) : '').trigger('change');

    const utcInput = (date) => date.toISOString().slice(0, 16);
    const setDateInput = (input, value) => {
        if (input._flatpickr) {
            input._flatpickr.setDate(value, false);
        } else {
            input.value = value;
        }
    };
    const utcDisplay = (value) => new Intl.DateTimeFormat('en-CA', {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: 'UTC',
        hour12: false,
    }).format(new Date(value)) + ' UTC';

    const showEditor = (session = null, values = null) => {
        currentSession = session;
        form.reset();
        form.hidden = false;
        summary.hidden = true;
        editButton.hidden = true;
        saveButton.hidden = false;
        deleteForm.hidden = true;
        method.disabled = !session;
        formMode.value = session ? 'edit' : 'create';
        form.action = session ? session.updateUrl : @json(route('training.instructingsessions.create'));
        document.getElementById('sessionModalTitle').textContent = session ? 'Edit Session' : 'New Session';
        document.getElementById('sessionModalSubtitle').textContent = 'Times are entered in UTC';

        const now = new Date();
        now.setUTCSeconds(0, 0);
        const later = new Date(now.getTime() + 60 * 60 * 1000);
        setStudent(values?.student_id ?? session?.studentId ?? '');
        title.value = values?.title ?? session?.title ?? '';
        setDateInput(start, values?.start_time ?? (session ? utcInput(new Date(session.start)) : utcInput(now)));
        setDateInput(end, values?.end_time ?? (session ? utcInput(new Date(session.end)) : utcInput(later)));
        comment.value = values?.instructor_comment ?? session?.comment ?? '';
        modal.show();
    };

    const showDetails = (session) => {
        currentSession = session;
        form.hidden = true;
        summary.hidden = false;
        editButton.hidden = false;
        saveButton.hidden = true;
        deleteForm.hidden = false;
        deleteForm.action = session.cancelUrl;
        document.getElementById('sessionModalTitle').textContent = session.title;
        document.getElementById('sessionModalSubtitle').textContent = 'Instructing session';
        document.getElementById('summaryStudent').textContent = session.student || 'Unknown';
        document.getElementById('summaryInstructor').textContent = session.instructor || 'Unknown';
        document.getElementById('summaryStart').textContent = utcDisplay(session.start);
        document.getElementById('summaryEnd').textContent = utcDisplay(session.end);
        const summaryComment = document.getElementById('summaryComment');
        summaryComment.textContent = session.comment || '';
        summaryComment.hidden = !session.comment;
        modal.show();
    };

    document.getElementById('newSessionButton').addEventListener('click', () => showEditor());
    editButton.addEventListener('click', () => showEditor(currentSession));

    document.querySelectorAll('[data-session-id]').forEach((row) => {
        row.addEventListener('click', () => showDetails(sessionsById.get(row.dataset.sessionId)));
    });

    const updateClock = () => {
        document.getElementById('utcClock').textContent = new Date().toISOString().slice(11, 19);
    };

    updateClock();
    setInterval(updateClock, 1000);

    const requestedSession = sessionsById.get(String(@json(request()->query('session'))));
    const requestedAction = @json(request()->query('action'));
    const oldValues = @json($oldSessionValues);

    if (requestedAction === 'new') {
        showEditor(null, oldValues);
    } else if (requestedSession && requestedAction === 'edit') {
        showEditor(requestedSession, oldValues);
    } else if (requestedSession) {
        showDetails(requestedSession);
    }
});
</script>
@endpush
