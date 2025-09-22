@extends('layouts.master')
@section('title', 'Booking - Vancouver FIR')
@section('description', 'Vancouver FIR\'s Booking')

@section('navbarprim')
    @parent
@stop

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

<style>
    #calendar {background: #1f1f2e; padding: 15px 15px 30px 15px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); color: #f8f9fa; }
    .fc-toolbar { background: #2c2c3a; border-radius: 8px; padding: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.4); }
    .fc-toolbar-title { font-weight: 700; color: #fff; font-size: 1.25rem; }
    .fc-button { background: #4b4b63; color: #fff; border: none; font-weight: 600; }
    .fc-button:hover { background: #6c6ca5; }
    .fc-col-header-cell { background: #2c2c3a; color: #f8f9fa; border: none; }
    .fc-day-today { background: #343454 !important; }
    .fc-event { border-radius: 6px; color: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.5); opacity: 0.9; }
    .fc-event:hover { opacity: 1; transform: scale(1.05); transition: 0.2s; }
    .fc-daygrid-day-frame { border: 1px solid rgba(255,255,255,0.05); }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #fff !important; }
    .select2-container--default .select2-selection--single { background-color: #333 !important; color: #fff !important; border: 1px solid #777 !important; }
    .select2-dropdown { background-color: #333 !important; color: #fff !important; }
    .select2-search__field { background-color: #333 !important; color: #fff !important; }
</style>

@section('content')
<div class="container my-4">

    <h1 class="blue-text">Booking – Calendar View</h1>

    <div class="row">
        <div class="col-md-8">
            <h5 id="utcClock" class="text-white mb-2" style="font-weight: bold;">--:--:-- UTC</h5>
            <small class="d-block mb-2">All times listed are in Zulu time!</small>

            <div class="mb-2" style="display: flex; align-items: center; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.3rem;">
                    <span class=" d-inline-block" style="width:12px; height:12px; background:#28a745;"> </span> <span> Booking </span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.3rem;">
                    <span class="d-inline-block" style="width:12px; height:12px; background:#007bff;"> </span> <span> Event </span>
                </div>
            </div>

            <div id="calendar"></div>
        </div>

        <div class="col">
            @if (Auth::check() && Auth::user()->certified())
            <div class="card mb-3">
                <div class="card-header">Bookings</div>
                <ul class="list-group list-group-flush">
                    <a class="list-group-item" data-toggle="collapse" href="#createBookingAccordion" role="button" aria-expanded="false" aria-controls="createBookingAccordion" onclick="openCreateAccordion()">Create a Booking</a>
                    <div class="collapse px-3 py-2" id="createBookingAccordion">
                        <form id="bookingFormAccordion" method="POST" action="{{ route('booking') }}">
                            @csrf
                            <input type="hidden" name="_method" id="formMethodAccordion" value="POST">
                            <input type="hidden" name="cid" value="{{ Auth::user()->id }}">

                            <div class="form-group">
                                <label for="callsignAccordion">Callsign</label>
                                <select class="form-control" name="callsign" id="callsignAccordion" required>
                                    <option value="">Select a callsign</option>
                                    @foreach($callsigns as $cs)
                                        <option value="{{ $cs }}" {{ ($booking->callsign ?? '') === $cs ? 'selected' : '' }}>
                                            {{ $cs }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @php
                                $nowUtc = now()->utc();
                                $oneHourLaterUtc = $nowUtc->copy()->addHour();
                            @endphp
                            <div class="form-group">
                                <label for="startAccordion">Start (UTC)</label>
                                <input type="datetime-local" class="form-control" name="start" id="startAccordion"
                                    value="{{ $nowUtc->format('Y-m-d\TH:i') }}"
                                    min="{{ $nowUtc->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="endAccordion">End (UTC)</label>
                                <input type="datetime-local" class="form-control" name="end" id="endAccordion"
                                    value="{{ $oneHourLaterUtc->format('Y-m-d\TH:i') }}"
                                    min="{{ $nowUtc->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2" id="bookingSubmitAccordion">Create Booking</button>
                        </form>
                    </div>
                    <a class="list-group-item view-bookings-toggle" data-toggle="collapse" href="#userBookings" role="button" aria-expanded="false" aria-controls="userBookings">View your Bookings</a>
                    <div class="collapse mt-2" id="userBookings">
                        <div class="list-group list-group-flush">
                            @forelse($bookings->where('cid', Auth::id()) as $b)
                                <div class="list-group-item bg-dark text-light d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $b['callsign'] }} ({{ \Carbon\Carbon::parse($b['start'])->format('d M H:i') }})
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-primary btn-sm edit-booking-btn" data-id="{{ $b['id'] }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('booking.delete', $b['id']) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="collapse mt-2 edit-booking-accordion" id="editBooking{{ $b['id'] }}">
                                        <form method="POST" class="bookingFormAccordion" data-id="{{ $b['id'] }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <label>Callsign</label>
                                                <input type="text" class="form-control callsignInput" name="callsign" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Start (UTC)</label>
                                                <input type="datetime-local" class="form-control startInput" name="start" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>End (UTC)</label>
                                                <input type="datetime-local" class="form-control endInput" name="end" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-success mt-2">Update Booking</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item bg-dark text-light py-3">
                                    No bookings found!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </ul>
            </div>
            @endif
                <div class="card mt-3">
                    <div class="card-header">Information</div>
                    <div class="list-group list-group-flush">
                        <a class="list-group-item" data-toggle="collapse" href="#controllerBookingInfo" role="button" aria-expanded="false" aria-controls="controllerBookingInfo">
                            What is a controller booking?
                        </a>
                        <div class="collapse px-3 py-2" id="controllerBookingInfo">
                            <p>A controller booking is a scheduled time slot during which a controller has reserved a position to control. Bookings help you see who is expected to be online and when. Please note that bookings are approximate and coverage for the entire period may not be guaranteed!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@php
    $cids = $bookings->pluck('cid')->toArray();
    $userLookup = \App\Models\Users\User::whereIn('id', $cids)
        ->get()
        ->mapWithKeys(fn($u) => [$u->id => trim($u->fname.' '.$u->lname)]);
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        height: 700,
        eventDisplay: 'block',
        eventTextColor: '#fff',
        headerToolbar: { 
            left: 'prev,next today', 
            center: 'title', 
            right: 'dayGridMonth,timeGridWeek,timeGridDay' 
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        events: {!! json_encode(
            collect($bookings)->map(fn($b) => [
                'id' => 'booking-'.$b['id'],
                'title' => "{$b['callsign']} (".($userLookup[$b['cid']] ?? $b['cid']).")",
                'callsign' => $b['callsign'],
                'name' => $userLookup[$b['cid']] ?? $b['cid'],
                'start' => $b['start'],
                'end'   => $b['end'],
                'backgroundColor' => '#28a745',
                'borderColor' => 'rgba(255,255,255,0.2)',
                'type' => 'booking',
            ])->merge(
                collect($events)->map(fn($e) => [
                    'id' => 'event-'.$e->id,
                    'title' => $e->name,
                    'start' => $e->start_timestamp,
                    'end'   => $e->end_timestamp,
                    'backgroundColor' => '#007bff',
                    'borderColor' => 'rgba(255,255,255,0.2)',
                    'type' => 'event',
                    'url' => 'https://czvr.ca/events/' . $e->slug,
                    'target' => '_blank'
                ])
            )->values()->all(),
            JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
        ) !!},
        eventContent(info) {
            const start = info.event.start;
            const end = info.event.end;
            const fmt = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
            const timeText = start && end ? `${fmt.format(start)} – ${fmt.format(end)}` : start ? fmt.format(start) : '';

            if (info.event.extendedProps.type === 'event') {
                return { html: `<div style="white-space: normal; line-height:1.1;"><a href="${info.event.url}" target="_blank" style="color:#fff; text-decoration:none;">${info.event.title}</a></div><div>${timeText}</div>` };
            }

            const callsign = info.event.extendedProps.callsign;
            const name = info.event.extendedProps.name;
            return { html: `<div style="white-space: normal; line-height:1.1;">${callsign} [${name}]</div><div>${timeText}</div>` };
        }
    });

    calendar.render();
});

$('#callsignAccordion').select2({
    width: '100%',
    placeholder: "Select a callsign"
});

function openCreateAccordion() {
    const now = new Date();
    const isoStart = now.toISOString().slice(0,16);
    const isoEnd = new Date(now.getTime() + 60*60*1000).toISOString().slice(0,16);

    document.getElementById('bookingFormAccordion').reset();
    document.getElementById('startAccordion').value = isoStart;
    document.getElementById('endAccordion').value = isoEnd;

    $('#callsignAccordion').val(null).trigger('change');
}

document.querySelectorAll('.edit-booking-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const accordion = document.getElementById('editBooking' + id);

        const bsCollapse = new bootstrap.Collapse(accordion, { toggle: true });

        fetch(`/${id}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);

            accordion.querySelector('.callsignInput').value = data.callsign ?? '';
            accordion.querySelector('.startInput').value = data.start?.replace(' ', 'T') ?? '';
            accordion.querySelector('.endInput').value = data.end?.replace(' ', 'T') ?? '';

            accordion.querySelector('.bookingFormAccordion').action = `/${id}`;
        });
    });
});

setInterval(() => {
    const now = new Date();
    document.getElementById('utcClock').textContent = now.toISOString().slice(11,19) + ' UTC';
}, 1000);

</script>

@stop
