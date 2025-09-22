@extends('layouts.email')

@section('to-line', 'Hello '. $student->user->fullName('FLC') . ',')


@section('message-content')
    <h1>You have failed to renew your training within 14 days and you have been marked for removal.</h1><br/>
    <p>To ensure our students on the waitlist are still interested in continuing their training, we periodically ask you to renew your training request.</p><br/>
    <p><a href="{{ url('/staff') }}" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin-bottom: 30px;">Contact Support</a></p>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket!</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line')
you applied for training at the Vancouver FIR
@endsection
