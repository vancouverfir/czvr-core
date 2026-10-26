@extends('layouts.master')
@section('title', 'Unsubscribe - Vancouver FIR')

@section('content')
    <div class="container py-4" style="max-width: 640px;">
        <h1 class="blue-text">Email Subscription</h1>
        <hr>
        @if ($done)
            <p>You have been unsubscribed and will no longer receive news or event emails from the Vancouver FIR!</p>
            <p class="text-muted">Emails about your account, tickets and training will still be sent! You can subscribe again at any time from your dashboard!</p>
            <a href="{{ route('index') }}" class="btn btn-primary">Go Home</a>
        @elseif (! $user->gdpr_subscribed_emails)
            <p>You are not subscribed to news or event emails, so there is nothing to do!</p>
            <a href="{{ route('index') }}" class="btn btn-primary">Go Home</a>
        @else
            <p>Unsubscribe from Vancouver FIR news and event emails?</p>
            <p class="text-muted">Emails about your account, tickets and training will still be sent!</p>
            <form method="POST" action="{{ request()->fullUrl() }}">
                @csrf
                <button type="submit" class="btn btn-danger">Unsubscribe</button>
                <a href="{{ route('index') }}" class="btn btn-light">Cancel</a>
            </form>
        @endif
    </div>
@stop
