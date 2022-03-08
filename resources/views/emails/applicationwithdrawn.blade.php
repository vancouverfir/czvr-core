@extends('layouts.email')

@section('to')

    <strong>Hi there,</strong>
@stop

@section('message-content')
    <p>A controller has withdrawn their visitor application for the Winnipeg FIR.</p>
    <b>Details</b>
    <ul>
        <li>Application ID: {{$application->application_id}}</li>
        <li>Name: {{$application->user->fullName('FLC')}}</li>
        <li>Rating/Division: {{$application->user->rating}}/{{$application->user->division}}</li>
    </ul>
    <hr>
    <br/>
    You can view their application <a href="{{route('training.viewapplication', $application->application_id)}}">here.</a>
@stop
@section('footer-reason-line')
    you are a Winnipeg FIR Executive.
@endsection
