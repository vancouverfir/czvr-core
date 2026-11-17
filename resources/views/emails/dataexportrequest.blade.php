@extends('layouts.email')

@section('headline', 'Your data export')

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    <p>Here is the data we hold about you, as you requested. To view this data easily, search for a JSON formatter online.</p>
    <div style="margin:0 0 24px 0; padding:14px 18px; background-color:#f5f5f7; border-radius:6px; font-family:Consolas, Menlo, monospace; font-size:12px; line-height:1.5; word-break:break-all;">{{ $json }}</div>
    <p>If you have any questions about your data, please open a support ticket.</p>
@endsection

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you requested a copy of your data from the Vancouver FIR.')
