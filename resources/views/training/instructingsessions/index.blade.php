@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

<style>
    #calendar {
        background: #323234ff;
        border-radius: 3px;
        padding: 15px 15px 30px 15px;
    }
    .fc-col-header-cell { background: #3d3d42ff; }
    .fc-day-today { background: #328d50ff !important; }
</style>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

@section('content')
@include('includes.trainingMenu')

<div class="container my-5">
    <h1>Instructing Sessions</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 id="utcClock" class="fw-bold m-0">--:--:-- UTC</h3>
        <a href="{{ route('training.instructingsessions.new') }}" class="btn btn-outline-primary">New Instructing Session</a>
    </div>

    <div id="calendar"></div>
</div>

<script type="module">
document.addEventListener('DOMContentLoaded', function () {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        height: 700,
        eventDisplay: 'block',
        eventTextColor: '#fff',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'UTC' },
        events: {!! json_encode(
            $upcomingSessions->map(fn($s) => [
                'id' => $s->id,
                'sessionTitle' => $s->title,
                'instructor' => $s->instructorUser() ? $s->instructorUser()->fullName('FLC') : null,
                'student' => ($s->student && $s->student->user) ? $s->student->user->fullName('FLC') : null,
                'start' => $s->start_time->utc()->format('Y-m-d\TH:i:s\Z'),
                'end' => $s->end_time->utc()->format('Y-m-d\TH:i:s\Z'),
                'backgroundColor' => '#007bff',
                'borderColor' => 'rgba(255,255,255,0.2)',
            ])->values()->all(),
            JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
        ) !!},
        eventContent(info) {
            const fmt = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'UTC' });
            const start = info.event.start, end = info.event.end;
            const timeText = start && end ? `${fmt.format(start)} – ${fmt.format(end)} UTC` : start ? fmt.format(start) + ' UTC' : '';
            const instructor = info.event.extendedProps.instructor;
            return { html: `<div><div>${timeText}</div><div><strong>${instructor}</strong></div></div>` };
        },
        eventDidMount(info) {
            const instructor = info.event.extendedProps.instructor;
            const student = info.event.extendedProps.student;
            const sessionTitle = info.event.extendedProps.sessionTitle;

            $(info.el).tooltip({
                title: `<div><strong>${sessionTitle}</strong></div>${instructor}<br>${student}<br>`,
                placement: 'top',
                trigger: 'hover',
                html: true,
                container: 'body'
            });
        },
        eventClick(info) {
            window.location.href = `/instructingsessions/${info.event.id}`;
        }
    });

    calendar.render();
});

setInterval(() => {
    const now = new Date();
    document.getElementById('utcClock').textContent = now.toISOString().slice(11,19) + ' UTC';
}, 1000);
</script>

@stop
