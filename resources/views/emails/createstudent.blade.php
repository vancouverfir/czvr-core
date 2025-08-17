@extends('layouts.email')

@section('to-line', 'Hi '. $student->user->fullName('FLC') . ',')


@section('message-content')
    <h1>You have been added as a Student!</h1><br/>
    <p>Welcome to Vancouver FIR!</p><br/>
    <p>Share your times so we can match you with the right Instructor!</p><br/>
    <a href="https://training.czvr.ca" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin-bottom: 30px;">Submit Availability</a><br/>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line')
you applied for training at the Vancouver FIR
@endsection
