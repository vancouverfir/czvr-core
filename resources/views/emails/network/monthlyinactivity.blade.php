@extends('layouts.email')

@section('headline', 'Monthly controller activity report')

@section('message-content')
    <p>These controllers have <strong>not</strong> met the minimum activity requirement this month. Each should be contacted by email or ticket about their inactivity.</p>

    @foreach (['visit0' => 'Home controllers', 'visit1' => 'Visiting controllers'] as $key => $heading)
        <h2 style="font-size:16px; font-weight:600; margin:24px 0 8px 0;">{{ $heading }}</h2>
        @if (count($members[$key]))
            <table cellpadding="0" cellspacing="0" role="presentation" style="min-width:60%; margin:0 0 8px 0; font-size:14px;">
                @foreach ($members[$key] as $m)
                    <tr>
                        <td style="padding:5px 12px 5px 0;">{{ $m['name'] }}<br><span style="color:#6e6e73;">{{ $m['email'] }}</span></td>
                        <td style="padding:5px 0; text-align:left; padding-left:24px; white-space:nowrap;">{{ $m['activity'] }} / {{ $m['requirement'] }} h</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p style="color:#6e6e73;">None.</p>
        @endif
    @endforeach
@endsection

@section('from-line', 'Sent automatically by ActivityBot')

@section('footer-reason-line', 'you are a Vancouver FIR staff member.')
