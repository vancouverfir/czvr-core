@extends('layouts.email')

@section('headline', 'Your training request has been renewed')

@section('to-line', 'Hi '.$student->user->display_fname.',')

@section('message-content')
    <p>Thanks for renewing your training request! No further action is required, this email serves only as a notification!</p>
    <p>If you have any questions, please open a support ticket.</p>
@stop

@section('footer-to-line', $student->user->email)

@section('footer-reason-line', 'you renewed your Vancouver FIR training request.')
