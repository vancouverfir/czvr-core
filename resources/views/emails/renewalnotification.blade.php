@extends('layouts.email')

@section('to-line', 'Hi '. $student->user->fullName('FLC') . ',')


@section('message-content')
    <h1>Your training request is set to expire! Please renew your training request by clicking the link below!</h1><br/>
    <p><a href="{{ $renewalLink }}" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:4px;">Renew Training</a></p><br/><br/>
    <p>If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.</p>
@stop

@section('footer-to-line', $student->user->fullName('FLC').' ('.$student->user->email.')')

@section('footer-reason-line')
you applied for training at the Vancouver FIR
@endsection
