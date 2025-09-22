@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')

@include('includes.trainingMenu')

<style>
    .select2-container--default .select2-selection--single {
        background-color: #333 !important;
        color: #fff !important;
        height: 30px !important;
        line-height: 30px !important;
        border-radius: 0 !important;
        border: 1px solid #555 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff !important;
    }
    .select2-dropdown {
        background-color: #333 !important;
        color: #fff !important;
    }
    .select2-search__field {
        background-color: #333 !important;
        color: #000000ff !important;
    }
    #instructor_comment {
        height: 90px;
        min-height: 0;
        font-size: 1rem;
        line-height: 1.5;
        padding: .375rem .75rem;
        resize: vertical;
}
</style>

<div class="container mt-3 mb-5">
    <h1>{{ isset($session) ? 'Edit Session' : 'Create Session' }}</h1>
    <hr/>

    <form action="{{ isset($session) ? route('training.instructingsessions.update', $session) : route('training.instructingsessions.create') }}" method="POST">
        @csrf
        @if(isset($session))
            @method('PUT')
        @endif

        <div class="form-group mt-3">
            <label for="instructor_id">Instructor</label>
            <select id="instructor_id" class="form-control select2" disabled>
                @php
                    $authInstructor = $instructors->firstWhere('user_id', auth()->id());
                @endphp

                @if($authInstructor)
                    <option value="{{ $authInstructor->id }}" selected>
                        {{ $authInstructor->user->fullName('FLC') }}
                    </option>
                @else
                    <option value="{{ auth()->id() }}" selected>
                        {{ auth()->user()->fullName('FLC') }}
                    </option>
                @endif
            </select>

            <input type="hidden" name="instructor_id" value="{{ auth()->id() }}">
        </div>

        <div class="form-group mt-3">
            <label for="student_id">Student</label>
            <select name="student_id" id="student_id" class="form-control select2" {{ isset($session) ? 'disabled' : 'required' }}>
                @if(isset($session))
                    <option value="{{ $session->student_id }}" selected>{{ $session->student->user->fullName('FLC') }}</option>
                @else
                    <option value="">Select a student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->user->fullName('FLC') }}</option>
                    @endforeach
                @endif
            </select>
            @if(isset($session))
                <input type="hidden" name="student_id" value="{{ $session->student_id }}">
            @endif
        </div>

        <div class="form-group mt-3">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $session->title ?? '' }}" required>
        </div>

        @php
            $nowUtc = now()->utc();
            $startUtc = isset($session) ? $session->start_time->utc()->format('Y-m-d\TH:i') : $nowUtc->format('Y-m-d\TH:i');
            $endUtc   = isset($session) ? $session->end_time->utc()->format('Y-m-d\TH:i') : $nowUtc->copy()->addHour()->format('Y-m-d\TH:i');
        @endphp

        <div class="form-group mt-3">
            <label for="start_time">Start Time</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control" min="{{ now()->utc()->format('Y-m-d\TH:i') }}" value="{{ isset($session) ? $session->start_time->utc()->format('Y-m-d\TH:i') : now()->utc()->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="end_time">End Time</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control" min="{{ now()->utc()->format('Y-m-d\TH:i') }}" value="{{ isset($session) ? $session->end_time->utc()->format('Y-m-d\TH:i') : now()->utc()->addHour()->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="instructor_comment">Instructor Comment (optional)</label>
            <textarea name="instructor_comment" id="instructor_comment" class="form-control">{{ $session->instructor_comment ?? '' }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-4">{{ isset($session) ? 'Update Session' : 'Create Session' }}</button>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#instructor_id').select2({ width: '100%' }).on('select2:opening select2:unselecting', function(e) {
        e.preventDefault();
    });

    $('#student_id').select2({
        placeholder: "Select a student",
        width: '100%'
    });
});
</script>

@stop
