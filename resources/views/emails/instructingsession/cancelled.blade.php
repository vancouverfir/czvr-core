@extends('layouts.email')

@section('message-content')

<h2>Instructing Session Cancelled</h2>
<p>
    @if($recipient === 'instructor')
        Hello {{ $session->instructorUser()->fullName('FLC') }},
    @else
        Hello {{ $session->student->user->fullName('FLC') }},
    @endif
</p>

<p>We regret to inform you that your instructing session has been cancelled:</p>

<ul>
    <li><strong>Title:</strong> {{ $session->title }}</li>
    <li><strong>Scheduled Start:</strong> {{ $session->start_time->format('d M Y H:i') }} UTC</li>
    <li><strong>Instructor:</strong> {{ $session->instructorUser()->fullName('FLC') }}</li>
</ul>

<p>We apologize for the inconvenience and will notify you of any rescheduling</p>

@endsection

@section('footer-reason-line')
your session was cancelled
@endsection