@extends('layouts.email')

@section('message-content')

<h2>Instructing Session Updated</h2>
<p>
    @if($recipient === 'instructor')
        Hello {{ $session->instructorUser()->fullName('FLC') }},
    @else
        Hello {{ $session->student->user->fullName('FLC') }},
    @endif
</p>

<p>Your instructing session has been updated. Here are the new details:</p>

<ul>
    <li><strong>Title:</strong> {{ $session->title }}</li>
    <li><strong>Start:</strong> {{ $session->start_time->format('d M Y H:i') }} UTC</li>
    <li><strong>End:</strong> {{ $session->end_time->format('d M Y H:i') }} UTC</li>
    <li><strong>Instructor:</strong> {{ $session->instructorUser()->fullName('FLC') }}</li>
    @if(!empty($session->instructor_comment))
        <li><strong>Instructor Comment:</strong> {{ $session->instructor_comment }}</li>
    @endif
</ul>

<p>Thank you!</p>

@endsection

@section('footer-reason-line')
your session was updated
@endsection