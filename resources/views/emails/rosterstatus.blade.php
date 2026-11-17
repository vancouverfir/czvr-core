@extends('layouts.email')

@section('headline', $controller->active == 1 ? "You're active on the roster again" : "You've been marked inactive on the roster")

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    @if ($controller->active == 1)
        <p>A member of staff has marked you as active again on the Vancouver FIR controller roster, so your certifications are back in effect.</p>
        <p>If you have any questions, please open a support ticket.</p>
    @else
        <p>A member of staff has marked you as inactive on the Vancouver FIR controller roster.</p>
        <p>If you'd like to become active again, or think this is a mistake, please open a support ticket.</p>
    @endif
@stop

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'your activity status on the Vancouver FIR controller roster changed.')
