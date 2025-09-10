@extends('layouts.email')

@section('message-content')

An unauthorised connection on a monitored position was detected at {{ $timestamp }} <br/>
<ul>
    <li>Callsign: {{$oc['callsign']}}</li>
    <li>CID: {{$oc['cid']}}</li>
</ul>

@endsection

@section('footer-reason-line')
you are on the Vancouver FIR Executive Team
@endsection