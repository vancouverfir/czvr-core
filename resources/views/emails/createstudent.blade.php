@extends('layouts.email')

@section('headline', 'Welcome to the Training Centre')

@section('to-line', 'Hi '.$student->user->display_fname.',')

@section('message-content')
    <p>You've been added as a student in the Vancouver FIR Training Centre!</p>
    <p>Whether you're starting out or joining us as a visitor, the Training Centre is where you'll find everything you need for your training.</p>
    <p>To help us pair you with the right instructor, please share your general availability. All times should be in UTC.</p>
    <p style="margin:8px 0 24px 0;"><a href="{{ 'https://training.czvr.ca' }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Submit Availability</a></p>
    <p>If you think this is a mistake or have any questions, please open a support ticket.</p>
@stop

@section('footer-to-line', $student->user->email)

@section('footer-reason-line', 'you were added as a student in the Vancouver FIR Training Centre.')
