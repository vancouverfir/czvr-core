@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')

@include('includes.trainingMenu')

<div class="container mt-3 mb-5">

    <a href="{{ route('training.instructingsessions.index') }}" class="d-inline-block mb-3 blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"> </i> Instructing Sessions </a>

    <h1>Session Details</h1>
    <hr/>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p><strong>Instructor:</strong> {{ $session->instructorUser()->fullName('FLC') }}</p>
            <p><strong>Student:</strong> {{ $session->student->user->fullName('FLC') }}</p>
            <p class="mb-3">
                <strong>Start Time:</strong> 
                {{ \Carbon\Carbon::parse($session->start_time)->format('d M Y, H:i') }} UTC 
                [{{ \Carbon\Carbon::parse($session->start_time)->diffForHumans() }}]
            </p>
            <p class="mb-3">
                <strong>End Time:</strong> 
                {{ \Carbon\Carbon::parse($session->end_time)->format('d M Y, H:i') }} UTC 
                [{{ \Carbon\Carbon::parse($session->end_time)->diffForHumans() }}]
            </p>

            @if ($session->instructor_comment)
                <div class="mt-3">
                    <strong>Instructor Comment:</strong>
                    <p class="p-2">{{ $session->instructor_comment }}</p>
                </div>
            @endif

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('training.instructingsessions.edit', $session->id) }}" class="btn btn-primary"> Edit Session </a>

                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal"> Cancel Session </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg p-3">

      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Cancellation!</h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body text-center">
        <p class="fs-5">Are you sure you want to cancel this session?</p>
      </div>

      <div class="modal-footer d-flex justify-content-center align-items-center gap-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>

        <form action="{{ route('training.instructingsessions.cancel', $session->id) }}" method="POST" class="m-0">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Cancel Session</button>
        </form>
      </div>

    </div>
  </div>
</div>

@stop
