@extends('layouts.email')

@section('message-content')


<h1>Reminder: Upcoming Instructing Session</h1>

<p>Hello {{ $recipient->fullName('FLC') }},</p>

<p>Your instructing session starts in 3 hours! Here are the details:</p>

<ul>
    <li><strong>Title:</strong> {{ $session->title }}</li>
    <li><strong>Start Time:</strong> {{ $session->start_time->format('Y-m-d H:i') }}</li>
    <li><strong>End Time:</strong> {{ $session->end_time->format('Y-m-d H:i') }}</li>
    <li><strong>Instructor:</strong> {{ $session->instructorUser()->fullName('FLC') }}</li>
    <li><strong>Student:</strong> {{ $session->student->user->fullName('FLC') }}</li>
    @if($session->instructor_comment)
        <li><strong>Instructor Comment:</strong> {{ $session->instructor_comment }}</li>
    @endif
</ul>

<p>Please make sure to be ready on time!</p>

@endsection

@section('footer-reason-line')
you have an upcoming instructing session
@endsection
