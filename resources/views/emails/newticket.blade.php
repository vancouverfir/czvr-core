@extends('layouts.email')

@section('headline', 'New ticket: '.$ticket->title)

@section('to-line', 'Hi '.$user->display_fname.',')

@section('message-content')
    <p>{{ $ticket->user->fullName('FLC') }} opened a new support ticket.</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter([
            'Ticket' => '#'.$ticket->ticket_id,
            'Opened' => \Illuminate\Support\Carbon::parse($ticket->submission_time)->format('D j M Y, H:i').' UTC',
        ], fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <div style="margin:0 0 24px 0; padding:14px 18px; background-color:#f5f5f7; border-radius:6px; color:#1d1d1f;">{{ $ticket->toHtml('message') }}</div>
    <p style="margin:8px 0 24px 0;"><a href="{{ route('tickets.viewticket', $ticket->ticket_id) }}" target="_blank" rel="noopener" style="display:inline-block; padding:8px 16px; background-color:#007bff; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:4px;">View Ticket</a></p>
@endsection

@section('footer-to-line', $user->email)

@section('footer-reason-line', 'you are a Vancouver FIR staff member.')
