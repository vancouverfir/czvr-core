@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
    #calendar {background: #1f1f2e; padding: 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); color: #f8f9fa; }
    .fc-toolbar { background: #2c2c3a; border-radius: 8px; padding: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.4); }
    .fc-toolbar-title { font-weight: 700; color: #fff; font-size: 1.25rem; }
    .fc-button { background: #4b4b63; color: #fff; border: none; font-weight: 600; }
    .fc-button:hover { background: #6c6ca5; }
    .fc-col-header-cell { background: #2c2c3a; color: #f8f9fa; border: none; }
    .fc-day-today { background: #343454 !important; }
    .fc-event { border-radius: 6px; color: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.5); opacity: 0.9; }
    .fc-event:hover { opacity: 1; transform: scale(1.05); transition: 0.2s; }
    .fc-daygrid-day-frame { border: 1px solid rgba(255,255,255,0.05); }
    .select2-container--default .select2-selection--single {
    background-color: #333 !important;
    color: #fff !important;
    border: 1px solid #777 !important;
    }
    .select2-dropdown {
    background-color: #333 !important;
    color: #fff !important;
    }
    .select2-search__field {
        background-color: #333 !important;
        color: #fff !important;
    }

</style>

@section('content')
<div class="container my-4">

    <h1 class="blue-text">Controller Booking</h1>

    <div class="row">
        <div class="col-md-8">
            <h4>Calendar View</h4>
            <small class="d-block mb-2">All times listed are in Zulu time!</small>
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
                                        <option value="{{ $cs }}" {{ old('callsign', $booking->callsign ?? '') === $cs ? 'selected' : '' }}>
                                            {{ $cs }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="startAccordion">Start (UTC)</label>
                                <input type="datetime-local" class="form-control" name="start" id="startAccordion" required>
                            </div>

                            <div class="form-group">
                                <label for="endAccordion">End (UTC)</label>
                                <input type="datetime-local" class="form-control" name="end" id="endAccordion" required>
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
                                                <input type="datetime-local" class="form-control startInput" name="start" required>
                                            </div>
                                            <div class="form-group">
                                                <label>End (UTC)</label>
                                                <input type="datetime-local" class="form-control endInput" name="end" required>
                                            </div>
                                            <button type="submit" class="btn btn-success mt-2">Update Booking</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="px-1 text-light">No Bookings found!</p>
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
                            <p>
                                I don't know what to put here!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap4',
        height: 'auto',
        eventDisplay: 'block',
        eventTextColor: '#fff',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        events: {!! json_encode(
            $bookings->map(fn($b) => [
                'id' => $b['id'],
                'title' => "{$b['callsign']} ({$b['cid']})",
                'start' => $b['start'],
                'end'   => $b['end'],
                'backgroundColor' => match($b['type'] ?? 'booking') {
                    'exam' => '#e74c3c',
                    'mentoring' => '#3498db',
                    'event' => '#9b59b6',
                    default => '#28a745',
                },
                'borderColor' => 'rgba(255,255,255,0.2)'
            ])->values()->all(),
            JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
        ) !!},
        eventDidMount(info) {
            const formatTimeOnly = (isoString) => {
                return isoString.substring(11, 16) + ' UTC';
            };

            const startUTC = formatTimeOnly(info.event.start.toISOString());
            const endUTC   = formatTimeOnly(info.event.end.toISOString());

            $(info.el).tooltip({
                title: `<strong>${info.event.title}</strong><br>${startUTC} – ${endUTC}`,
                placement: 'top',
                trigger: 'hover',
                html: true,
                container: 'body'
            });
        },
        eventClick(info) { openEditModal(info.event.id); }
    });

    calendar.render();
});

$('#callsignAccordion').select2({
    theme: 'bootstrap4',
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

</script>
@stop
