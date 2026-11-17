@extends('layouts.email')

@section('headline', 'Please renew your training request')

@section('to-line', 'Hi '.$student->user->display_fname.',')

@section('message-content')
    <p>To make sure everyone on the waitlist is still interested in continuing their training wtih us, we ask students to confirm their training request each month.</p>
    <p style="margin:8px 0 24px 0;"><a href="{{ $renewalLink }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Renew Training Request</a></p>
    <p>If you don't renew, you'll be marked for removal from the waitlist. If you have any questions, please open a support ticket.</p>
@stop

@section('footer-to-line', $student->user->email)

@section('footer-reason-line', 'you have a training request with the Vancouver FIR.')
