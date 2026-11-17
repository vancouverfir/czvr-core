@extends('layouts.email')

@section('headline', 'Unauthorised connection detected')

@section('message-content')
    <p>ActivityBot detected a connection on a monitored Vancouver position by a controller who isn't certified for it.</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter([
            'Callsign' => $oc['callsign'],
            'CID' => $oc['cid'],
            'Detected' => $timestamp,
        ], fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('from-line', 'Sent automatically by ActivityBot')

@section('footer-reason-line', 'you are on the Vancouver FIR Executive Team.')
