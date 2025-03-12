@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h1>Instructing Sessions</h1>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Upcoming Sessions
                    </div>
                    <div class="card-body">
                        @if ($sessions !== null)
                        <div class="list-group">
                            @foreach ($upcomingSessions as $session)
                            <a href="{{route('training.instructingsessions.viewsession', $session->id)}}" class="list-group-item d-flex justify-content-between align-items-center @if (Auth::user()->instructingProfile === $session->instructor) bg-primary @endif">
                                {{$session->student->user->fullName('FLC')}}<br/>
                                {{$session->type}} | {{$session->date}} {{$session->start_time}} to {{$session->end_time}}<br/>
                                Instructor: {{$session->instructor->user->fullName('FLC')}}
                            </a>
                            @endforeach
                        </div>
                        @else
                        No upcoming sessions.
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Actions
                    </div>
                    <div class="card-body">
                    <a href="{{ route('training.instructingsessions.createsession') }}" role="button" class="btn btn-primary">Create Session</a>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <h3>Calendar</h3>
            <script type='importmap'>
                {
                    "imports": {
                    "@fullcalendar/core": "https://cdn.skypack.dev/@fullcalendar/core@6.1.15",
                    "@fullcalendar/daygrid": "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.15",
                    "@fullcalendar/interaction":"https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.15/+esm"
                    }
                }
            </script>

            <script type='module'>
                import { Calendar } from '@fullcalendar/core'
                import interactionPlugin from '@fullcalendar/interaction'
                import dayGridPlugin from '@fullcalendar/daygrid'

                const calendarEl = document.getElementById('calendar')
                const calendar = new Calendar(calendarEl, {
                    timeZone: 'UTC',
                    headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                plugins: [
                    interactionPlugin,
                    dayGridPlugin
                ],
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,
                events: [
                    { title: 'Meeting', start: new Date() }
                ]
                })
                    calendar.render()
            </script>
        <div class="card-body" id='calendar'></div>
        <br>
        <br>
    </div>
@stop
