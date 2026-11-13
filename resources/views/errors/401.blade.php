@extends('layouts.master')
@section('title', 'Error 401')

@section('content')
    <div class="container py-5">
        <h1 class="fw-bold blue-text"><i class="fa fa-cogs"></i> You can't access this page</h1>
        <hr>
        <h3 class="fw-bold blue-text">Error 401</h3>
        <p>
            You are not permitted to access this page. You may not have the required permissions.
            <br>
            If you believe this is a mistake, please contact us.
        </p>
        <p class="border rounded p-3">
            {{ $exception->getMessage() }} - Unauthorized
        </p>
        <a href="{{route('index')}}" class="btn btn-primary">Go Home</a>
    </div>
@endsection
