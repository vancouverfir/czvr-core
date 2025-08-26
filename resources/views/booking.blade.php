@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <h1 class="blue-text">Controller Booking</h1>
        <div class="row">
            <div class="col-md-8">
                <h4>Upcoming</h4>
                <small>All times listed are in Zulu time!</small>
                @foreach($upcomingBookings as $b)
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $b['callsign'] }}</h5>
                                <p>
                                    @php
                                        $start = \Carbon\Carbon::parse($b['start']);
                                        echo $start->diffForHumans();
                                    @endphp
                                </p>
                            </div>
                            @php
                                $start_time = \Carbon\Carbon::parse($b['start']);
                                $end_time = \Carbon\Carbon::parse($b['end']);
                            @endphp
                            <p class="mb-1">
                                Booked by {{$b['cid']}}<br/>
                                From {{$start_time->toDayDateTimeString()}} to {{$end_time->toDayDateTimeString()}}
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="col">
                @if (Auth::check() && Auth::user()->certified())
                <div class="card">
                    <div class="card-header">Bookings</div>
                    <ul class="list-group list-group-flush">
                        <a href="#" class="list-group-item" data-toggle="modal" data-target="#bookingModal">Create a booking</a>
                        <a href="#" class="list-group-item">View your booking</a>
                    </ul>
                </div>
                @endif
                <div class="card mt-3">
                    <div class="card-header">Information</div>
                    <ul class="list-group list-group-flush">
                        <a href="#" class="list-group-item">What is a controller booking?</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="bookingForm" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bookingModalLabel">Booking</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="cid" value="{{ Auth::check() ? Auth::user()->id : '' }}">

            <div class="form-group">
                <label for="callsign">Callsign</label>
                <input type="text" class="form-control" name="callsign" id="callsign" required>
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" name="type" id="type">
                    <option value="booking">Booking</option>
                    <option value="event">Event</option>
                    <option value="exam">Exam</option>
                    <option value="mentoring">Mentoring</option>
                </select>
            </div>

            <div class="form-group">
                <label for="start">Start (UTC)</label>
                <input type="datetime-local" class="form-control" name="start" id="start" required>
            </div>

            <div class="form-group">
                <label for="end">End (UTC)</label>
                <input type="datetime-local" class="form-control" name="end" id="end" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="bookingSubmitBtn">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateModal() {
    const now = new Date();
    const isoStart = now.toISOString().slice(0,16);
    const isoEnd = now.toISOString().slice(0,16);

    document.getElementById('bookingForm').action = "{{ route('booking') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('bookingForm').reset();
    document.getElementById('bookingModalLabel').textContent = 'Create Booking';
    document.getElementById('bookingSubmitBtn').textContent = 'Create';

    document.getElementById('start').value = isoStart;
    document.getElementById('end').value = isoEnd;
}

function openEditModal(id) {
    fetch("{{ url('/booking') }}/" + id + "/edit", { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
            document.getElementById('bookingForm').action = "{{ url('/booking') }}/" + id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('bookingModalLabel').textContent = 'Edit Booking';
            document.getElementById('bookingSubmitBtn').textContent = 'Update';

            document.getElementById('callsign').value = data.callsign;
            document.getElementById('type').value = data.type;
            document.getElementById('start').value = data.start.replace(' ', 'T');
            document.getElementById('end').value = data.end.replace(' ', 'T');
        });
}
</script>
@stop
