@extends('layouts.email')

@section('headline', 'New '.$kind.' feedback')

@section('message-content')
    <p>{{ $intro }}</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter($details, fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin:0 0 24px 0; padding:14px 18px; background-color:#f5f5f7; border-radius:6px; color:#1d1d1f; white-space:pre-line;">{{ $content }}</div>
    <p style="margin:8px 0 24px 0;"><a href="{{ $url }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Feedback</a></p>
@stop

@section('footer-reason-line', 'you receive '.$kind.' feedback for the Vancouver FIR.')
