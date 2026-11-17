@extends('layouts.email')

@section('headline', 'New Event: '.$event->name)

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    @if ($event->image_url)
        <p><img src="{{ $event->image_url }}" width="560" alt="{{ $event->name }}" style="display:block; width:100%; max-width:560px; border-radius:4px;"></p>
    @endif
    @php
        $details = [
            'Date' => $event->start_timestamp->format('D j M Y, H:i').' – '.($event->start_timestamp->isSameDay($event->end_timestamp) ? $event->end_timestamp->format('H:i') : $event->end_timestamp->format('D j M Y, H:i')).' UTC',
        ];
        if ($event->departure_icao && $event->arrival_icao) {
            if ($event->departure_icao === $event->arrival_icao) {
                $details['Airports'] = $event->departure_icao;
            } else {
                $details['Route'] = $event->departure_icao.' → '.$event->arrival_icao;
            }
        }
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter($details, fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    {{ $event->toHtml('description') }}
    <p style="margin:8px 0 24px 0;"><a href="{{ url('/events/'.$event->slug) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Event</a></p>
@endsection

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you are subscribed to Vancouver FIR emails.')
