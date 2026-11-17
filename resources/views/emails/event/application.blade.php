@extends('layouts.email')

@section('headline', 'Thanks for applying to control '.$event->name)

@section('to-line', 'Hi '.$app->user->display_fname.',')

@section('message-content')
    <p>We've received your application to control this event. The Events Team will confirm positions closer to the date.</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter([
            'Event' => $event->name,
            'Date' => \Illuminate\Support\Carbon::parse($event->start_timestamp)->format('D j M Y, H:i').' – '.\Illuminate\Support\Carbon::parse($event->end_timestamp)->format('H:i').' UTC',
            'Airport' => $app->airport,
            'Your availability' => \Illuminate\Support\Carbon::parse($app->start_availability_timestamp)->format('H:i').' – '.\Illuminate\Support\Carbon::parse($app->end_availability_timestamp)->format('H:i').' UTC',
        ], fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/events/'.$event->slug) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Event</a></p>
    <p>If you need to change your application, please <a href="{{ url('/staff') }}">contact the Events Team</a>.</p>
@stop

@section('footer-to-line', $app->user->email)

@section('footer-reason-line', 'you applied to control a Vancouver FIR event.')
