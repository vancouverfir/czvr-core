@extends('layouts.email')

@section('headline', count($members) ? 'Quarterly visiting controller report' : 'Quarterly visiting controller report: all clear')

@section('message-content')
    @if (count($members))
        <p>These visiting controllers did less than 50% of their controlling on Vancouver positions in the past quarter.</p>
        <table cellpadding="0" cellspacing="0" role="presentation" style="min-width:60%; margin:0 0 24px 0; font-size:14px;">
            <tr>
                <td style="padding:5px 12px 5px 0; color:#6e6e73;">Controller</td>
                <td style="padding:5px 0; color:#6e6e73; text-align:left; padding-left:24px;">On Vancouver positions</td>
            </tr>
            @foreach ($members as $m)
                <tr>
                    <td style="padding:5px 12px 5px 0;">{{ $m['name'] }}</td>
                    <td style="padding:5px 0; text-align:left; padding-left:24px;">{{ round($m['percentage'] * 100, 1) }}%</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>No visiting controllers fell below the 50% Vancouver controlling requirement in the past quarter.</p>
    @endif

    @if (isset($unknown) && count($unknown))
        <h2 style="font-size:16px; font-weight:600; margin:24px 0 8px 0; color:#b45309;">Some controllers couldn't be checked</h2>
        <p>The VATSIM API didn't return usable data for these controllers after repeated attempts, please check them manually!</p>
        <table cellpadding="0" cellspacing="0" role="presentation" style="min-width:60%; margin:0 0 24px 0; font-size:14px;">
            @foreach ($unknown as $controller)
                <tr>
                    <td style="padding:5px 12px 5px 0;">{{ $controller['name'] }}</td>
                    <td style="padding:5px 0; text-align:left; padding-left:24px; color:#6e6e73;">{{ $controller['cid'] }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection

@section('from-line', 'Sent automatically by ActivityBot')

@section('footer-reason-line', 'you are a Vancouver FIR staff member.')
