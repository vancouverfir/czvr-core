@extends('layouts.email')

@section('to-line', 'Hi '. $user->fullName('FLC') . ',')

@section('message-content')
<h2>{{$news->title}}</h2>
{{$news->toHtml('content')}}
@endsection

@section('from-line')
@if ($news->show_author)
Sent by <b>{{ $news->user->fullName('FLC') }}@if ($news->user->staffProfile) ({{ $news->user->staffProfile->position }})@endif</b>
@else
Sent by the Vancouver FIR Staff Team
@endif
@endsection

@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')

@section('footer-reason-line')
you hold an account on the Vancouver FIR website and have subscribed to emails.
@endsection
