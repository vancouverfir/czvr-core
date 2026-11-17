@extends('layouts.email')

@section('headline', 'New reply on your ticket')

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    <p>{{ $reply->user->fullName('FLC') }} replied to ticket #{{ $ticket->ticket_id }}, {{ $ticket->title }}.</p>
    <div style="margin:0 0 24px 0; padding:14px 18px; background-color:#f5f5f7; border-radius:6px; color:#1d1d1f;">{{ $reply->toHtml('message') }}</div>
    <p style="margin:8px 0 24px 0;"><a href="{{ route('tickets.viewticket', $ticket->ticket_id) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Ticket</a></p>
@endsection

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you are part of an open Vancouver FIR support ticket.')
