@extends('layouts.master')
@section('title', '403 - Access Denied')

@section('content')
    <div class="container py-5 text-center">
        <h1>403</h1>
        <p>You don’t have permission to access this page.</p>
        <p>
            Please log in</a> or check your account permissions!
        </p>
        <small class="text-muted">
            {{ $exception->getMessage() ?: 'You are not authorized to view this page!' }}
        </small>
    </div>
@endsection
