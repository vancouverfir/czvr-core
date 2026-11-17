@extends('layouts.email')

@section('headline', 'Welcome to the Vancouver FIR')

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    <p>Welcome to the Vancouver FIR! We're very exited that you're here!</p>
    <p>The website is home to everything Vancouver - from controller resources, the roster, training, events, contact info and more!</p>
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/') }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Visit the Website</a></p>
@endsection

@section('from-line')
    Thanks,<br>
    Josh Jenkins<br>
    <span style="color:#6e6e73;">Vancouver FIR Chief (ZVR1)</span>
@endsection

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you signed in to the Vancouver FIR website for the first time.')
