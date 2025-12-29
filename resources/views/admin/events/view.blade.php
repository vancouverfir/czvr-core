@extends('layouts.master')

@section('content')

<style>
  .roster-table th, .roster-table td {
    border: 1px solid #333 !important;
    vertical-align: middle;
  }
  .roster-table tbody tr:hover {
    background: #2b2b2b;
  }

  .sticky-col {
    position: sticky;
    left: 0;
    background: #111;
    z-index: 5;
    border-right: 2px solid #333 !important;
  }
  .left-col {
    min-width: 160px;
  }

  .controller-block {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    padding: 6px;
    font-weight: 600;
    font-size: .9rem;
    color: #fff;
    position: relative;
    transition: all .2s ease-in-out;
  }
  .controller-block:hover {
    box-shadow: inset 0 0 0 2px #fff3;
    z-index: 2;
  }

  .position-name {
    font-size: .85rem;
    font-weight: 700;
    text-transform: uppercase;
  }

  .controller-delete {
    position: absolute;
    top: 4px;
    right: 6px;
    display: none;
  }
  .controller-block:hover .controller-delete {
    display: block;
  }
</style>

<div class="container py-4">
    <a href="{{route('events.admin.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Events</a>
    <h1 class="font-weight-bold blue-text">Managing: "{{$event->name}}"</h1>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <h4 class="font-weight-bold blue-text">Actions</h4>
            <ul class="list-unstyled mt-3 mb-0" style="font-size: 1.05em;">
                <li class="mb-2">
                    <a href="" data-toggle="modal" data-target="#editEvent" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Edit event details</span></a>
                </li>
                <li class="mb-2">
                    <a href="" data-toggle="modal" data-target="#createUpdate" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Create update</span></a>
                </li>
                <li class="mb-2">
                    <a href="" data-toggle="modal" data-target="#confirmController" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Add Controller to Event Roster</span></a>
                </li>
                <li class="mb-2">
                    <a href="{{route('event.viewapplications', [$event->id]) }}" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">View Controller Applications</span></a>
                </li>
                <li class="mb-2">
                    <a href="" data-toggle="modal" data-target="#deleteEvent" style="text-decoration:none;"><span class="red-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Delete event</span></a>
                </li>

            {{-- <li class="mb-2">
                    <a href="" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Export controller applications</span></a>
                </li> --}}
            </ul>
        </div>
        <div class="col-md-9">
          <h4 class="font-weight-bold blue-text">Details</h4>
          <div class="row">
              <div class="col-md-6">
                  <table class="table table-borderless table-striped">
                      <tbody>
                          <tr>
                              <td>Start Time</td>
                              <td>{{$event->start_timestamp_pretty()}}</td>
                          </tr>
                          <tr>
                              <td>End Time</td>
                              <td>{{$event->end_timestamp_pretty()}}</td>
                          </tr>
                          <tr>
                              <td>Departure Airport</td>
                              <td>{{$event->departure_icao}}</td>
                          </tr>
                          <tr>
                              <td>Arrival Airport</td>
                              <td>{{$event->arrival_icao}}</td>
                          </tr>
                      </tbody>
                  </table>
              </div>
              <div class="col-md-6">
                  @if ($event->image_url)
                  <img src="{{$event->image_url}}" alt="" class="img-fluid w-50 img-thumbnail">
                  @else
                  No image!
                  @endif
              </div>
          </div>
            <hr>
          <h4 class="font-weight-bold blue-text">Description</h4>
            {{$event->description}}<hr>
          <h4 class="font-weight-bold blue-text">Updates</h4>
            @if (count($updates) == 0)
                None yet!
            @else
                @foreach($updates as $u)
                    <div class="card p-3">
                        <h4>{{$u->title}}</h4>
                        <div class="d-flex flex-row align-items-center">
                            <i class="far fa-clock"></i>&nbsp;&nbsp;Created {{$u->created_pretty()}}</span>&nbsp;&nbsp;•&nbsp;&nbsp;<i class="far fa-user-circle"></i>&nbsp;&nbsp;{{$u->author_pretty()}}&nbsp;&nbsp;•&nbsp;&nbsp;<a href="{{route('events.admin.update.delete', [$event->slug, $u->id])}}" class="red-text">Delete</a>
                        </div>
                        <hr>
                        {{$u->toHtml('content')}}
                    </div>
                    <br>
                @endforeach
            @endif

            <hr>

            <h4 class="font-weight-bold text-light mt-3">Event Roster</h4>

            @if (count($eventroster) < 1)
            <div class="alert alert-secondary">Nobody is confirmed to control yet!</div>
            @else
                <div class="card bg-dark text-light shadow-sm p-3">
                    <div class="table-responsive" style="max-height: 600px; overflow: auto;">
                    <table class="table table-sm mb-0 text-center roster-table">
                        <thead class="sticky-top">
                            <tr>
                                <th class="align-middle text-white sticky-col left-col">Controller</th>
                                @php
                                $start = strtotime($event->start_timestamp);
                                $end   = strtotime($event->end_timestamp);
                                $slots = [];
                                while ($start <= $end) {
                                    $slots[] = date("H:i", $start) . "z";
                                    $start = strtotime("+30 minutes", $start);
                                }

                                $positionColors = [
                                    'CTR' => '#4553ce',
                                    'APP' => '#4f9fd3',
                                    'DEP' => '#4f9fd3',
                                    'TWR' => '#c35454',
                                    'GND' => '#7dac50',
                                    'DEL' => '#5889e1',
                                ];
                                @endphp

                                @foreach($slots as $slot)
                                    <th class="small font-weight-bold text-muted px-2">{{ $slot }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($eventroster->groupBy('user_id') as $userId => $assignments)
                            @php
                            $user = $assignments->first()->user;
                            $slotIndex = 0;
                            $totalSlots = count($slots);
                            @endphp
                            <tr>
                            <td class="font-weight-bold text-light sticky-col left-col">
                                {{ $user->fullName('FL') }}
                            </td>

                            @while ($slotIndex < $totalSlots)
                                @php
                                $slotTime = strtotime($event->start_timestamp) + ($slotIndex * 1800);

                                $assignment = $assignments->first(function($a) use($slotTime) {
                                    return $slotTime >= strtotime($a->start_timestamp)
                                        && $slotTime < strtotime($a->end_timestamp);
                                });
                                @endphp

                                @if($assignment)
                                @php
                                    $mergeCount = 1;
                                    while (
                                    $slotIndex + $mergeCount < $totalSlots &&
                                    strtotime($event->start_timestamp) + (($slotIndex + $mergeCount) * 1800) < strtotime($assignment->end_timestamp)
                                    ) {
                                    $mergeCount++;
                                    }
                                    $parts = explode('_', $assignment->airport);
                                    $pos = strtoupper(end($parts)); // CTR, APP, DEP, etc.
                                    $color = $positionColors[$pos] ?? '#6c757d';
                                @endphp
                                <td colspan="{{ $mergeCount }}" class="p-0 controller-cell">
                                    <div class="controller-block" style="background-color: {{ $color }};" data-toggle="tooltip" title="{{ $assignment->airport }} {{ date('H:i', strtotime($assignment->start_timestamp)) }}z → {{ date('H:i', strtotime($assignment->end_timestamp)) }}z">
                                    <span class="position-name">{{ $assignment->airport }}</span>
                                    <form method="POST" action="{{ route('event.deletecontroller', [$assignment->user_id]) }}" class="controller-delete">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $assignment->event_id }}">
                                        <button type="submit" class="btn btn-sm btn-link text-light p-0"><i class="fas fa-trash"></i></button>
                                    </form>
                                    </div>
                                </td>
                                @php $slotIndex += $mergeCount; @endphp
                                @else
                                <td></td>
                                @php $slotIndex++; @endphp
                                @endif
                            @endwhile
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
</script>

<!--Delete event modal-->

<div class="modal fade" id="deleteEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This will soft delete the event, so it still exists in the database but cannot be viewed!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                <a href="{{route('events.admin.delete', $event->slug)}}" role="button" class="btn btn-danger">Delete Event</a>
            </div>
            </form>
        </div>
    </div>
</div>

<!--End delete event modal-->

<!--Edit event modal-->

<div class="modal fade" id="editEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit {{$event->name}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('events.admin.edit.post', $event->slug)}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($errors->editEventErrors->any())
                    <div class="alert alert-danger">
                        <h4>There were errors editing the event!</h4>
                        <ul class="pl-0 ml-0 list-unstyled">
                            @foreach ($errors->editEventErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <ul class="stepper mt-0 p-0 stepper-vertical">
                        <li class="active">
                            <a href="#!">
                                <span class="circle">1</span>
                                <span class="label">Primary information</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <label for="">Event name</label>
                                    <input type="text" name="name" id="" class="form-control" value="{{$event->name}}">
                                </div>
                                <div class="form-group">
                                    <label for="">Start date and time</label>
                                    <input type="datetime" name="start" value="{{$event->start_timestamp}}" placeholder="Put event start date/time here" class="form-control flatpickr" id="event_start">
                                </div>
                                <div class="form-group">
                                    <label for="">End date and time</label>
                                    <input type="datetime" name="end" value="{{$event->end_timestamp}}" placeholder="Put event end date/time here" class="form-control flatpickr" id="event_end">
                                </div>
                                <div class="form-group">
                                    <label for="">Departure airport ICAO (optional)</label>
                                    <input maxlength="4" type="text" value="{{$event->departure_icao}}" name="departure_icao" id="" class="form-control" placeholder="CYYC">
                                </div>
                                <div class="form-group">
                                    <label for="">Arrival airport ICAO (optional)</label>
                                    <input maxlength="4" type="text" value="{{$event->arrival_icao}}" name="arrival_icao" id="" class="form-control" placeholder="EIDW">
                                </div>
                                <script>
                                    flatpickr('#event_start', {
                                        enableTime: true,
                                        noCalendar: false,
                                        dateFormat: "Y-m-d H:i",
                                        time_24hr: true,
                                    });
                                    flatpickr('#event_end', {
                                        enableTime: true,
                                        noCalendar: false,
                                        dateFormat: "Y-m-d H:i",
                                        time_24hr: true,
                                    });
                                </script>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">2</span>
                                <span class="label">Description</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <label for="">Use Markdown</label>
                                    <textarea id="contentMD" name="description" class="w-75">{{$event->description}}</textarea>
                                    <script>
                                        var simplemde = new SimpleMDE({ element: document.getElementById("contentMD"), toolbar: false });
                                    </script>
                                </div>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">3</span>
                                <span class="label">Image</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                @if ($event->image_url)
                                    <img src="{{$event->image_url}}" alt="" class="img-fluid w-50 img-thumbnail">
                                @else
                                    No image!
                                @endif
                                <p>An image can be displayed for the event! Please ensure we have the right to use the image, and that it is of an acceptable resolution! Make sure the image has no text or logos on it!</p>
                                <div class="input-group pb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image">
                                        <label class="custom-file-label">Choose image</label>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">4</span>
                                <span class="label">Options</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <div class="mr-2">
                                        <input type="checkbox" class="" name="openControllerApps" id="openControllerApps" {{ $event->controller_applications_open == "1" ? "checked=checked" : ""}}>
                                        <label class="" for="">Open controller applications</label>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->editEventErrors->any())
<script>
    $("#editEvent").modal('show');
</script>
@endif

<!--End edit event modal-->


<!--Create update modal-->

<div class="modal fade" id="createUpdate" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create Event Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('events.admin.update.post', $event->slug)}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($errors->createUpdateErrors->any())
                    <div class="alert alert-danger">
                        <ul class="pl-0 ml-0 list-unstyled">
                            @foreach ($errors->createUpdateErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" name="updateTitle" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Use Markdown</label>
                        <textarea id="updateContent" name="updateContent"></textarea>
                        <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("updateContent"), toolbar: false });
                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($errors->createUpdateErrors->any())
<script>
    $("#createUpdate").modal('show');
</script>
@endif

<!--End app update modal-->

<!--Add Confirmed controller modal-->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="modal fade" id="confirmController" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Controller to Event {{$event->name}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

              <div align="center" class="modal-body">
                    <form id="app-form" method="POST" action="{{ route('event.addcontroller', [$event->id] )}}">
                    <div class="form-group row">
                        <label for="dropdown" class="col-sm-4 col-form-label text-md-right">Pick a controller!</label>

                        <select class="custom-select" name="user_cid">
                            @foreach($users as $user)
                            <option value="{{ $user->id}}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                            @endforeach
                        </select>

                        <div class="col-md-12">

                            <td align="center">
                                <input type="hidden" name="event_id" value="{{$event->id}}">
                                <input type="hidden" name="event_name" value="{{$event->name}}">
                                <input type="hidden" name="event_date" value="{{$event->start_timestamp}}">
                                <label for="">Start Time (zulu)</label>
                                <input type="datetime-local" name="start_timestamp" class="form-control" id="start_timestamp" value="{{ ($event->start_timestamp) }}" min="{{ ($event->start_timestamp) }}" max="{{ ($event->end_timestamp) }}">
                                <label class="mt-2" for="">End Time (zulu)</label>
                                <input type="datetime-local" name="end_timestamp" class="form-control" id="end_timestamp" value="{{ ($event->end_timestamp) }}" min="{{ ($event->start_timestamp) }}" max="{{ ($event->end_timestamp) }}">
                                <label class="mt-2" for="">Airport Callsign (e.g. CZVR_CTR)</label>
                                <input type="text" name="airport" class="form-control" id="airport">
                                @csrf
                            </td>

                        </div>
                    </div>
            </div>

            <div align="center" class="modal-footer">
              <div align="center"><button type="submit" class="btn btn-success">Confirm Controller</button></div>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></form>
            </div>
        </div>
    </div>
</div>

<!--End confirmed controller modal-->
