@extends('layouts.email')

@section('headline', $event->name.' starts tomorrow')

@section('to-line', 'Hi '.$positions->first()->user->display_fname.',')

@section('message-content')
    <p>{{ $event->name }} starts in 24 hours! You're confirmed for the following:</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter($positions->mapWithKeys(fn ($p) => [
            $p->airport => \Illuminate\Support\Carbon::parse($p->start_timestamp)->format('D j M, H:i').' – '.\Illuminate\Support\Carbon::parse($p->end_timestamp)->format('H:i').' UTC',
        ])->all(), fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/events/'.$event->slug) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Event</a></p>
    <p>If you can no longer make it, please <a href="{{ url('/staff') }}">contact the Events Coordinator</a> as soon as possible.</p>
@stop

@section('footer-to-line', $positions->first()->user->email)

@section('footer-reason-line', 'you are confirmed to control a Vancouver FIR event.')
