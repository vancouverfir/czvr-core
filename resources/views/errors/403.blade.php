@extends('layouts.master')
@section('title', '403 - Access Denied')

@section('content')
    <div class="container py-5">
        <h1 class="fw-bold blue-text"><i class="fa fa-cogs"></i> Access denied</h1>
        <hr>
        <h3 class="fw-bold blue-text">Error 403</h3>
        <p>You don’t have permission to access this page.</p>
        <p>
            Please <a href="{{ route('auth.connect.login') }}">log in</a> or check your account permissions!
        </p>
        <p class="border rounded p-3">
            {{ $exception->getMessage() ?: 'You are not authorized to view this page!' }}
        </p>
        <a href="{{ route('index') }}" class="btn btn-primary">Go Home</a>
    </div>
@endsection
