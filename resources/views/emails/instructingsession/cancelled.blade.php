@extends('layouts.email')

@php($to = $recipient === 'instructor' ? $session->instructorUser() : $session->student->user)

@section('headline', 'Training session cancelled')

@section('to-line', 'Hi '.$to->display_fname.',')

@section('message-content')
    <p>The following training session has been cancelled.</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter([
            'Session' => $session->title,
            'Was scheduled for' => $session->start_time->format('D j M Y, H:i').' UTC',
            'Instructor' => $session->instructorUser()->fullName('FL'),
        ], fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <p>Sorry for the inconvenience. You'll be emailed again if it's rescheduled.</p>
@endsection

@section('footer-to-line', $to->email)

@section('footer-reason-line', 'a Vancouver FIR training session you are part of was cancelled.')
