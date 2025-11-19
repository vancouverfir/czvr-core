@extends('layouts.email')

@section('to-line', 'Hi '. $student->user->fullName('FLC') . ',')


@section('message-content')
    <h1>You have been added as a Student!</h1><br/>
    <p>Welcome to Vancouver FIR!</p><br/>
    <p>We’re excited to let you know that you’ve been added as a student in our Training Portal!</p><br/>
    <p>Whether you’re just starting out or applied to visit, our portal is your hub for all the information you’ll need for your training at Vancouver FIR!</p><br/>
    <p>To help us pair you with the right instructor, please share your availability. Make sure your times are in UTC!</p><br/>
    <a href="https://training.czvr.ca" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px; margin-bottom: 30px;">Submit Availability</a><br/>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line') you've been added to the waitlist for CZVR FIR @endsection
