@extends('layouts.email')

@section('to-line', 'Hi '.$student->user->fullName('FLC').',')

@section('message-content')
    <h1>Your training renewal deadline is approaching!</h1><br/>
    <p>You have three days remaining to confirm that you would like to keep your place on the Vancouver FIR training waitlist!</p><br/>
    <p>Please renew by {{ $student->renewalDeadlineAt()?->format('F j, Y H:i') }} UTC!</p><br/>
    <p><a href="{{ $renewalLink }}" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin-bottom:30px;">Renew My Training Request</a></p><br/>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line')
you applied for training at the Vancouver FIR
@endsection
