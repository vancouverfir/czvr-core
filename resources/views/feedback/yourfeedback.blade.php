@extends('layouts.master')

@section('title', 'Your Feedback - Vancouver FIR')

@section('description', 'View the feedback that the VATSIM community has submitted to us!')

@push('styles')
    <link href="{{ asset('css/pages/feedback.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="container py-4 feedback-wall">
    <header class="feedback-wall-header mb-4">
        <div>
            <h1 class="blue-text">Your Feedback</h1>
            <p>Here’s some of the great feedback from VATSIM users who’ve flown through Vancouver! Your suggestions and experiences help us become better controllers!</p>
        </div>
        @auth
            <a href="{{ route('feedback.create') }}" class="btn btn-primary flex-shrink-0">
                Submit feedback
            </a>
        @endauth
    </header>

    @if ($feedback->isNotEmpty())
        <div class="feedback-wall-grid">
            @foreach ($feedback as $f)
                <article class="feedback-quote">
                    <div class="feedback-quote-header">
                        <span class="feedback-quote-controller">{{ $f->controller?->fullName('FL') ?? $f->controller_cid }}</span>
                    </div>
                    <blockquote>{{ $f->content }}</blockquote>
                    <footer class="feedback-station">
                        <i class="fa-solid fa-tower-broadcast" aria-hidden="true"></i>
                        <strong>{{ $f->position }}</strong>
                    </footer>
                </article>
            @endforeach
        </div>
    @else
        <div class="feedback-wall-empty">
            <h2 class="h5">Be the first to share your feedback with us!</h2>
        </div>
    @endif
</div>
@endsection
