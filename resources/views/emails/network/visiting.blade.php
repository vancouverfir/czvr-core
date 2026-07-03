@extends('layouts.email')

@section('message-content')
    <style>
        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .warning {
            color: #b45309;
            font-weight: bold;
        }
    </style>

    @if(count($members) > 0)
        <h2>Visiting Violations!</h2>

        <p>
            Here is a list of controllers who have committed less than 50% of their controlling
            at Vancouver in the past quarter:
        </p>

        <table style="width: 100%">
            <thead>
            <tr>
                <th class="border">Name</th>
                <th class="border">% on Vancouver Positions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $m)
                <tr>
                    <td class="border" style="text-align: left">{{ $m['name'] }}</td>
                    <td class="border" style="text-align: center">
                        {{ round($m['percentage'] * 100, 1) }}%
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <h2>No Visiting Violations</h2>

        <p>
            No controllers were found below the 50% Vancouver controlling requirement
            for the past quarter.
        </p>
    @endif

    @if(isset($unknown) && count($unknown) > 0)
        <br>

        <h2 class="warning">Unable to Check Some Controllers</h2>

        <p>
            The following controllers could not be checked because the VATSIM API did not return
            usable data after repeated attempts:
        </p>

        <table style="width: 100%">
            <thead>
            <tr>
                <th class="border">Name</th>
                <th class="border">CID</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unknown as $controller)
                <tr>
                    <td class="border" style="text-align: left">{{ $controller['name'] }}</td>
                    <td class="border" style="text-align: center">{{ $controller['cid'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p>
            These controllers were not counted as compliant or non-compliant in this report.
        </p>
    @endif
@endsection

@section('footer-reason-line')
    you are a staff member.
@endsection
