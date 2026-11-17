@extends('layouts.master')

@section('title', __('Server Error'))

@section('content')
    <div class="container py-5">
        <h1 class="fw-bold blue-text"><i class="fa fa-cogs"></i> Uh oh... (500 Server Error)</h1>
        <hr>
        <h3 class="fw-bold blue-text">It appears our server didn't like that very much...</h3>
        <p>Please submit a ticket to our <a href="{{url('/dashboard/tickets?create=yes')}}">webmaster</a> with:</p>
        <ul>
            <li>What you were trying to access</li>
            <li>What URL you were on</li>
            <li>What time (in z) you tried to access at</li>
            <li>Any other relevant information</li>
        </ul>
        <a href="{{ route('index') }}" class="btn btn-primary">Go Home</a>
    </div>
@stop
