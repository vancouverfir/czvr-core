@extends('layouts.master')

@section('title', 'Your Feedback - Vancouver FIR')

@section('description', 'View the feedback that the VATSIM community has submitted to us!')

@section('content')

<div class="container py-4">
    <h1 class="font-weight-bold blue-text mb-3">Your Feedback</h1>
    <p class="lead mb-4"> Here’s some of the great feedback from VATSIM users who’ve flown through Vancouver! Your suggestions and experiences help us become better controllers. Haven’t submitted feedback yet? You can do so <a href="/feedback" class="blue-text">here</a>!</p>

    <hr class="bg-light">

    @forelse($feedback as $f)
        <div class="card mb-3">
            <div class="card-body">
                <h5><strong>Controller: </strong>{{ User::where('id', $f->controller_cid)->first()->fullName('FL') }}</h5>
                <p>"{{ $f->content }}"</p>
            </div>
        </div>
@empty
    <div class="text-center py-5">
        <h3>Nothing yet! Be the first to share your feedback with us! ✈️</h3>
    </div>
@endforelse

</div>

@endsection
