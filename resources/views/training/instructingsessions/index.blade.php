@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@push('styles')
<style>
    #calendar {
        background: var(--czvr-surface);
        border: 1px solid var(--czvr-border);
        border-radius: 0.75rem;
        color: var(--czvr-text);
        min-height: 650px;
        padding: 15px;
    }

    .session-event {
        background: var(--czvr-green) !important;
        border: 1px solid var(--czvr-green-dark) !important;
        border-radius: 0.35rem;
        color: var(--czvr-white) !important;
        cursor: pointer;
        display: block !important;
        overflow: hidden;
        text-decoration: none;
        width: 100% !important;
    }

    .session-event:hover,
    .session-event:focus-visible {
        background: var(--czvr-green-dark) !important;
    }

    .session-event * { color: inherit !important; }
    .session-calendar-event {
        display: block;
        line-height: 1.25;
        padding: 4px 6px;
    }
    .session-calendar-event strong { font-weight: 500; }
    .session-calendar-event small { display: block; font-size: var(--czvr-font-size-base); opacity: .9; }

    .session-summary-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .session-summary-item {
        border-bottom: 1px solid var(--czvr-border);
        padding-bottom: 0.75rem;
    }

    .session-summary-item span {
        color: var(--czvr-text-muted);
        display: block;
        font-size: var(--czvr-font-size-base);
        margin-bottom: 0.2rem;
        text-transform: uppercase;
    }

    .session-summary-item strong {
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
    }

    .session-comment {
        background: var(--czvr-surface-muted);
        border-radius: 0.5rem;
        font-size: var(--czvr-font-size-base);
        margin-top: 1rem;
        padding: 0.85rem;
        white-space: pre-wrap;
    }

    @media (max-width: 768px) {
        #calendar { min-height: 520px; padding: 8px; }
    }

    @media (max-width: 575.98px) {
        .session-summary-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@push('vendor-styles')
    <link href="{{ asset('vendor/fullcalendar/fullcalendar.css') }}" rel="stylesheet">
@endpush
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
@endphp

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <h1 class="blue-text">Instructing Sessions</h1>
            <p>Plan and review training sessions! All times are shown in UTC!</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="utcClock" class="small text-muted">--:--:-- UTC</span>
            <button type="button" id="newSessionButton" class="btn btn-sm btn-primary">New session</button>
        </div>
    </header>

    <div id="calendar"></div>
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
<script src="{{ asset('vendor/fullcalendar/fullcalendar.global.js') }}"></script>
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

    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        toolbarClass: 'calendar-toolbar',
        toolbarSectionClass: 'calendar-toolbar-section',
        toolbarTitleClass: 'calendar-toolbar-title',
        buttonGroupClass: 'calendar-button-group',
        buttonClass: (button) => `calendar-button calendar-${button.name}-button`,
        height: 'auto',
        dayMaxEvents: true,
        eventDisplay: 'block',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        events: sessions,
        eventClass: 'session-event',
        eventContent(info) {
            const fmt = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'UTC' });
            const timeText = info.event.start && info.event.end
                ? `${fmt.format(info.event.start)} – ${fmt.format(info.event.end)} UTC`
                : '';
            const wrapper = document.createElement('div');
            wrapper.className = 'session-calendar-event';
            const eventTitle = document.createElement('strong');
            eventTitle.textContent = info.event.title;
            const details = document.createElement('small');
            details.textContent = [timeText, info.event.extendedProps.instructor].filter(Boolean).join(' · ');
            wrapper.append(eventTitle, details);
            return { domNodes: [wrapper] };
        },
        eventClick(info) {
            showDetails(sessionsById.get(String(info.event.id)));
        },
    });

    calendar.render();
    requestAnimationFrame(() => calendar.updateSize());

    const updateClock = () => {
        document.getElementById('utcClock').textContent = new Date().toISOString().slice(11, 19) + ' UTC';
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
