@extends('layouts.email')

@section('headline', 'Three days left to renew your training request')

@section('to-line', 'Hi '.$student->user->display_fname.',')

@section('message-content')
    <p>This is a reminder that your training request still needs to be renewed to keep your place on the Vancouver FIR waitlist.</p>
    <p style="margin:8px 0 24px 0;"><a href="{{ $renewalLink }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Renew Training Request</a></p>
    <p>If you have any questions, please open a support ticket.</p>
@stop

@section('footer-to-line', $student->user->email)

@section('footer-reason-line', 'you have a training request with the Vancouver FIR.')
