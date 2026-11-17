@extends('layouts.email')

@section('headline', 'Your training session starts in 3 hours')

@section('to-line', 'Hi '.$recipient->display_fname.',')

@section('message-content')
    <p>This is a reminder about your upcoming training session.</p>
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:4px 0 24px 0;">
        @foreach (array_filter([
            'Session' => $session->title,
            'Date' => $session->start_time->format('D j M Y'),
            'Time' => $session->start_time->format('H:i').' – '.$session->end_time->format('H:i').' UTC',
            'Instructor' => $session->instructorUser()->fullName('FL'),
            'Student' => $session->student->user->fullName('FL'),
            'Instructor comment' => $session->instructor_comment,
        ], fn ($value) => filled($value)) as $label => $value)
            <tr>
                <td width="140" style="padding:4px 16px 4px 0; color:#6e6e73; width:140px; vertical-align:top;">{{ $label }}</td>
                <td style="padding:4px 0; color:#1d1d1f; vertical-align:top;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <p>Please make sure you're ready on time.</p>
@endsection

@section('footer-to-line', $recipient->email)

@section('footer-reason-line', 'you have an upcoming Vancouver FIR training session.')
