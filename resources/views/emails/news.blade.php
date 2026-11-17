@extends('layouts.email')

@section('headline', 'News: '.$news->title)

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    {{ $news->toHtml('content') }}
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/news/'.$news->slug) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">Read on the website</a></p>
@endsection

@if ($news->show_author)
    @section('from-line')
        {{ $news->user->fullName('FL') }}@if ($news->user->staffProfile)<br><span style="color:#6e6e73;">{{ $news->user->staffProfile->position }}</span>@endif
    @endsection
@endif

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you are subscribed to Vancouver FIR news emails.')
