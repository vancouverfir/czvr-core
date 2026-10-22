@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="blue-text fw-bold mt-2">Email Preferences</h1>
        <hr>
        <h5>Current subscription status:</h5>
        @if (auth()->user()->gdpr_subscribed_emails == 0)
            <h3>
                <span class="badge text-bg-danger">Not subscribed</span>
            </h3>
        @else
            <h3>
                <span class="badge text-bg-success">Subscribed</span>
            </h3>
        @endif
        <br/>
        <h4>What does this mean?</h4>
        <p>
            When you subscribe to emails, you allow Vancouver FIR to send you what the EU GDPR describes as "promotional" emails.
            These emails are typically not necessary to your continued participation in the FIR or holding an account on the Core system.<br/>
            Some examples could include:
        </p>
        <ul style="list-style: square">
            <li>Controller certifications for the month</li>
            <li>News from the FIR Chief about non-critical matters</li>
            <li>Updates from other staff members</li>
        </ul>
        <p><br/>
            To see more info, check out our <a href="{{url('/privacy')}}">privacy policy!</a>
        </p><br/>
        <h4>Subscribe</h4>
        <br/>
        <form method="POST" action="{{url('/dashboard/emailpref/subscribe')}}" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">Subscribe to emails</button>
        </form>
        <form method="POST" action="{{url('/dashboard/emailpref/unsubscribe')}}" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger">Unsubscribe from emails</button>
        </form>
    </div>
@stop
