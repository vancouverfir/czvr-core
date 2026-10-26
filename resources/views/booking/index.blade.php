@extends('layouts.master')
@section('title', 'Booking - Vancouver FIR')
@section('description', 'Vancouver FIR\'s Booking')

@section('navbarprim')
    @parent
@stop

@push('styles')
    <link href="{{ asset('css/pages/training-instructor-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages/booking.css') }}?v={{ filemtime(public_path('css/pages/booking.css')) }}" rel="stylesheet">
@endpush

@php
    $now = now('UTC');
    $canBook = auth()->check() && auth()->user()->certified();
    $canManageBookings = auth()->check() && auth()->user()->can('bookings.manage');

    $bookingUsers = \App\Models\Users\User::whereIn('id', collect($bookings)->pluck('cid'))->get()->keyBy('id');
    $allBookings = collect($bookings)
        ->map(fn ($b) => [
            'type' => 'booking',
            'id' => $b['id'],
            'title' => $b['callsign'],
            'start' => \Carbon\Carbon::parse($b['start'], 'UTC'),
            'end' => \Carbon\Carbon::parse($b['end'], 'UTC'),
            'name' => isset($bookingUsers[$b['cid']]) ? trim($bookingUsers[$b['cid']]->fullName('FL')) : (string) $b['cid'],
            'mine' => $canBook && (int) $b['cid'] === (int) auth()->id(),
            'editable' => $canManageBookings || ($canBook && (int) $b['cid'] === (int) auth()->id()),
        ])
        ->filter(fn ($b) => $b['end']->gt($now))
        ->sortBy(fn ($b) => $b['start']->timestamp)
        ->values();

    $onNow = $allBookings->filter(fn ($b) => $b['start']->lte($now))->values();
    $next = $allBookings->first(fn ($b) => $b['start']->gt($now));

    $byDay = $allBookings->groupBy(fn ($b) => $b['start']->copy()->max($now)->format('Y-m-d'));
    $dayName = function ($date) use ($now) {
        $day = \Carbon\Carbon::parse($date, 'UTC');

        return $day->isSameDay($now) ? 'Today' : ($day->isSameDay($now->copy()->addDay()) ? 'Tomorrow' : $day->format('D j M'));
    };

    $span = function ($from, $to) {
        $minutes = (int) floor(abs($from->diffInMinutes($to)));
        $hours = intdiv($minutes, 60);

        if ($hours >= 48) {
            return intdiv($hours, 24).' days';
        }

        return trim(($hours ? $hours.'h ' : '').($minutes % 60 || ! $hours ? ($minutes % 60).'m' : ''));
    };
@endphp

@section('content')
<main class="container py-4 training-portal booking-page">
    <header class="training-portal-header">
        <div>
            <h1 class="blue-text">Booking</h1>
            <p>All times are UTC! Bookings are approximate and coverage isn't guaranteed!</p>
        </div>
        <div class="booking-header-side">
            <span class="booking-clock" aria-label="Current time in UTC"><span id="utcClock">--:--:--</span> <small>UTC</small></span>
            @if ($canBook)
                <button type="button" class="btn btn-primary btn-sm flex-shrink-0" data-booking-create>Book a position</button>
            @endif
        </div>
    </header>

    <div class="booking-strip">
        <span class="booking-strip-part">
            <span class="booking-strip-label">Booked now</span>
            @forelse ($onNow as $booking)
                <span><strong>{{ $booking['title'] }}</strong> · {{ $booking['name'] }} · until {{ $booking['end']->format('H:i') }} UTC</span>
            @empty
                <span>Nothing booked</span>
            @endforelse
        </span>
        @if ($next)
            <span class="booking-strip-part">
                <span class="booking-strip-label">Next</span>
                <span><strong>{{ $next['title'] }}</strong> · {{ $next['start']->isSameDay($now) ? '' : $next['start']->format('D j M').', ' }}{{ $next['start']->format('H:i') }} UTC · in {{ $span($now, $next['start']) }}</span>
            </span>
        @endif
    </div>

    <section class="training-panel" aria-labelledby="upcoming-bookings-heading">
        <div class="training-panel-header">
            <h2 id="upcoming-bookings-heading">Upcoming Bookings</h2>
        </div>

        @forelse ($byDay as $date => $items)
            <div class="booking-day-label">{{ $dayName($date) }}</div>
            @foreach ($items as $booking)
                @if ($booking['editable'])
                    <button type="button" class="training-session-row booking-row-button" data-booking-edit="{{ route('booking.edit', $booking['id']) }}" data-update-url="{{ route('booking.update', $booking['id']) }}" data-delete-url="{{ route('booking.delete', $booking['id']) }}">
                @else
                    <div class="training-session-row">
                @endif
                    <span class="training-session-date">
                        <span>{{ $booking['start']->format('M') }}</span>
                        <strong>{{ $booking['start']->format('d') }}</strong>
                    </span>
                    <span class="training-row-copy">
                        <span class="training-row-title">{{ $booking['title'] }}</span>
                        <span class="training-row-meta">{{ $booking['start']->format('H:i') }}–{{ $booking['end']->format('H:i') }} UTC · {{ $booking['name'] }}</span>
                    </span>
                    @if ($booking['start']->lte($now))
                        <span class="training-status training-status-open">Booked now</span>
                    @elseif ($booking['mine'])
                        <span class="training-status booking-status-mine">Yours</span>
                    @endif
                    @if ($booking['editable'])
                        <i class="fas fa-chevron-right booking-row-chevron" aria-hidden="true"></i>
                    @endif
                @if ($booking['editable'])
                    </button>
                @else
                    </div>
                @endif
            @endforeach
        @empty
            <div class="training-empty">No upcoming bookings!</div>
        @endforelse
    </section>
</main>

@if ($canBook)
    @php
        $defaultStart = $now->copy()->format('Y-m-d\TH:i');
        $defaultEnd = $now->copy()->addHour()->format('Y-m-d\TH:i');
    @endphp
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content booking-modal">
                <form id="bookingForm" method="POST" action="{{ route('booking.create') }}" data-create-url="{{ route('booking.create') }}">
                    @csrf
                    <input type="hidden" name="_method" id="bookingFormMethod" value="POST">
                    <input type="hidden" name="cid" value="{{ auth()->id() }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalTitle">Book a position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bookingCallsign" class="form-label">Position</label>
                            <select class="form-select" name="callsign" id="bookingCallsign" required data-searchable>
                                <option value="">Select a callsign</option>
                                @foreach ($callsigns as $cs)
                                    <option value="{{ $cs }}" @selected(old('callsign') === $cs)>{{ $cs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label for="bookingStart" class="form-label">Start (Z)</label>
                                <input type="datetime-local" class="form-control" name="start" id="bookingStart"
                                       value="{{ old('start', $defaultStart) }}" min="{{ $defaultStart }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="bookingEnd" class="form-label">End (Z)</label>
                                <input type="datetime-local" class="form-control" name="end" id="bookingEnd"
                                       value="{{ old('end', $defaultEnd) }}" min="{{ $defaultStart }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger me-auto d-none" id="bookingDelete">Delete booking</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="bookingSubmit">Book position</button>
                    </div>
                </form>
                <form id="bookingDeleteForm" method="POST" action="#" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endif
@stop

@push('scripts')
<script>
(() => {
    'use strict';

    // Live UTC clock in the header
    const clock = document.getElementById('utcClock');
    const tick = () => { clock.textContent = new Date().toISOString().slice(11, 19); };
    tick();
    setInterval(tick, 1000);

    const modalElement = document.getElementById('bookingModal');

    if (!modalElement) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const form = document.getElementById('bookingForm');
    const method = document.getElementById('bookingFormMethod');
    const title = document.getElementById('bookingModalTitle');
    const submit = document.getElementById('bookingSubmit');
    const remove = document.getElementById('bookingDelete');
    const deleteForm = document.getElementById('bookingDeleteForm');
    const callsign = document.getElementById('bookingCallsign');
    const start = document.getElementById('bookingStart');
    const end = document.getElementById('bookingEnd');

    // Works for plain inputs and for the flatpickr/select2 versions from form-controls.js
    const setDate = (input, value) => input._flatpickr ? input._flatpickr.setDate(value, false) : (input.value = value);
    const setCallsign = (value) => {
        if (value && !Array.from(callsign.options).some((option) => option.value === value)) {
            callsign.add(new Option(value, value));
        }
        callsign.value = value;
        if (window.jQuery) {
            jQuery(callsign).trigger('change');
        }
    };
    const toInputValue = (date) => date.toISOString().slice(0, 16);

    const openCreate = () => {
        form.action = form.dataset.createUrl;
        method.value = 'POST';
        title.textContent = 'Book a position';
        submit.textContent = 'Book position';
        remove.classList.add('d-none');
        setCallsign('');
        const now = new Date();
        setDate(start, toInputValue(now));
        setDate(end, toInputValue(new Date(now.getTime() + 60 * 60 * 1000)));
        modal.show();
    };

    // Takes a row's dataset with the booking's URLs
    const openEdit = (urls) => {
        form.action = urls.updateUrl;
        deleteForm.action = urls.deleteUrl;
        method.value = 'PUT';
        title.textContent = 'Edit booking';
        submit.textContent = 'Save changes';
        remove.classList.remove('d-none');
        modal.show();

        fetch(urls.bookingEdit, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => {
                setCallsign(data.callsign ?? '');
                setDate(start, (data.start ?? '').replace(' ', 'T').slice(0, 16));
                setDate(end, (data.end ?? '').replace(' ', 'T').slice(0, 16));
            });
    };

    remove.addEventListener('click', () => {
        if (confirm('Delete this booking?')) {
            deleteForm.submit();
        }
    });

    document.querySelectorAll('[data-booking-create]').forEach((button) => button.addEventListener('click', openCreate));
    document.querySelectorAll('[data-booking-edit]').forEach((button) => button.addEventListener('click', () => openEdit(button.dataset)));

    // A rejected booking comes back with errors: reopen the form with what was entered
    @if ($errors->any() && old('callsign'))
        modal.show();
    @endif
})();
</script>
@endpush
