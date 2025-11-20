@extends('layouts.master')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('events.admin.view', $event->slug)}}" class="blue-text" style="font-size: 1.2em;"> <i
                class="fas fa-arrow-left"></i> {{$event->name}}</a>
        <br>
        <h4 class="font-weight-bold blue-text mt-3">Event Applications for "{{$event->name}}"</h4>
        <hr>
        @if (count($applications) == 0)
            None yet!
            <br></br>
        @else

        <div class="row mb-1 container py-1">
        @foreach($applications as $a)
            <div class="col-lg-4 mb-3" style="padding-right: 1%">
                <div class="card" style="min-height: 100%">
                    <div class="card-header">
                        {{$a->user->fullName('FLC')}} ({{$a->user->rating_short}})
                    </div>
                    <div class="card-body">
                        <b class="mt-2" for="">Airport</b>
                        {{ $a->airport }} <br>
                        <b>Available: </b>
                        {{$a->start_availability_timestamp}} to {{$a->end_availability_timestamp}}<br>
                        <b>Email: </b>
                        {{$a->user->email}}
                        <br>
                        @if ($a->comments == true)
                            <b>Comments: </b>{{$a->comments}}<br>
                        @else
                            <b>Comments: </b>None<br>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="" data-toggle="modal" class="btn btn-sm btn-success"
                            data-target="#confirmApp{{$a->id}}">Confirm Controller</a>
                        <a href="{{route('events.admin.controllerapps.delete', [$event->slug, $a->user_id])}}"
                            class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </div>
            </div>


    <!--Confirm Appliation modal-->

    <div class="modal fade" id="confirmApp{{$a->id}}" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm Controller - {{$a->user->fullName('FL')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div align="center" class="modal-body">

                    <div class="form-group row">

                        <div class="col-md-12">


                            <form id="app-form" method="POST"
                                  action="{{ route('event.confirmapplication', [$event->id] )}}">

                                <td align="center">
                                    <input type="hidden" name="event_id" value="{{$event->id}}">
                                    <input type="hidden" name="event_name" value="{{$event->name}}">
                                    <input type="hidden" name="event_date" value="{{$event->start_timestamp}}">
                                    <input type="hidden" name="user_cid" value="{{$a->user_id}}">
                                    <input type="hidden" name="user_name" value="{{$a->user->fullName('FL')}}">
                                    <input type="hidden" name="airport" value="{{ $a->airport }}">
                                    <label for="">Start Time (zulu)</label>
                                    <input type="datetime" name="start_timestamp" class="form-control flatpickr"
                                           id="start_timestamp">
                                    <label class="mt-2" for="">End Time (zulu)</label>
                                    <input type="datetime" name="end_timestamp" class="form-control flatpickr"
                                           id="end_timestamp">
                                    <label class="mt-2">Airport</label>
                                    <p align="left">{{ $a->airport }}</p>
                                    @csrf
                                </td>
                            <script>
                                flatpickr('#start_timestamp', {
                                    enableTime: true,
                                    dateFormat: "Y-m-d H:i",
                                    time_24hr: true,
                                    defaultDate: "{{$a->start_availability_timestamp}}"
                                });

                                flatpickr('#end_timestamp', {
                                    enableTime: true,
                                    dateFormat: "Y-m-d H:i",
                                    time_24hr: true,
                                    defaultDate: "{{$a->end_availability_timestamp}}"
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <div align="center" class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm Controller</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--End confirm application modal-->

    @endforeach
    </div>
    @endif
@endsection
