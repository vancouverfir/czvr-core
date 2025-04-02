@extends('layouts.email')
@section('to-line', 'Hi '. $user->fullName('FLC') . '!')
@section('message-content')
<p>Welcome to the Vancouver FIR, we're very excited that you're here!</p>
<p>Welcome to Vancouver! This is the home to all things Vancouver - from controller files, to roster info, to training, contact info and more. Thanks for stopping by!</p>
@endsection
@section('from-line')
Thanks,<br/>
<b>Josh Jenkins</b><br>
<b>Vancouver FIR Chief (ZVR1)</b>
@endsection
@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')
@section('footer-reason-line', 'as they just logged into the Vancouver FIR website for the first time.')
