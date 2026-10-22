@extends('layouts.email')

@section('to-line', 'Hi '.$student->user->fullName('FLC').',')

@section('message-content')
    <h1>Your training request was renewed successfully!</h1><br/>
    <p>We're sending you this email to confirm the successful renewal of your training request!</p><br/>
    <p>No further action is required, this email serves only as a notification!</p><br/>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line')
you renewed your Vancouver FIR training request
@endsection
