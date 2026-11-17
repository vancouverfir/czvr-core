@extends('layouts.email')

@section('headline', 'Your training request has expired')

@section('to-line', 'Hi '.$student->user->display_fname.',')

@section('message-content')
    <p>Your training request wasn't renewed within 14 days and you have been marked for removal from the Vancouver FIR waitlist.</p>
    <p>We ask students to renew their training request each month to make sure everyone on the waitlist is still interested in continuing their training.</p>
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/staff') }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Contact Us</a></p>
    <p>If you think this is a mistake, please open a support ticket.</p>
@stop

@section('footer-to-line', $student->user->email)

@section('footer-reason-line', 'you have a training request with the Vancouver FIR.')
