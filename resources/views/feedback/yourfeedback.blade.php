@extends('layouts.master')

@section('title', 'Your Feedback - Vancouver FIR')

@section('description', 'View the feedback that the VATSIM community has submitted to us!')

@push('styles')
<style>
    .feedback-wall {
        max-width: 900px;
    }

    .feedback-wall-header {
        align-items: flex-end;
        border-bottom: 1px solid var(--czvr-border);
        display: flex;
        gap: 1.5rem;
        justify-content: space-between;
        padding-bottom: 1.25rem;
    }

    .feedback-wall-header h1 {
        margin-bottom: 0.35rem;
    }

    .feedback-wall-header p {
        color: var(--czvr-text-muted);
        font-size: 0.92rem !important;
        line-height: 1.55;
        margin: 0;
        max-width: 680px;
    }

    .feedback-wall-grid {
        display: grid;
        gap: 1rem;
    }

    .feedback-quote {
        background: var(--czvr-surface);
        border: 1px solid var(--czvr-border);
        border-radius: 0.7rem;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 1.2rem 1.25rem 1.05rem;
    }

    .feedback-quote-header {
        align-items: flex-start;
        display: flex;
        gap: 0.5rem;
        height: 1.45rem;
    }

    .feedback-quote-header::before {
        color: var(--czvr-green);
        content: '\201C';
        font-family: Georgia, serif;
        font-size: 2.3rem;
        line-height: 1;
    }

    .feedback-quote-controller {
        color: var(--czvr-green);
        font-size: var(--czvr-font-size-base);
        font-weight: 600;
        line-height: 1;
        padding-top: 0.1rem;
    }

    .feedback-quote blockquote {
        color: var(--czvr-text);
        flex: 1;
        font-size: 0.9rem;
        line-height: 1.65;
        margin: 0.55rem 0 1.1rem;
    }

    .feedback-station {
        align-items: center;
        border-top: 1px solid var(--czvr-border);
        color: var(--czvr-text-muted);
        display: flex;
        font-size: var(--czvr-font-size-base);
        gap: 0.5rem;
        padding-top: 0.8rem;
    }

    .feedback-station i {
        color: var(--czvr-green);
        width: 0.9rem;
    }

    .feedback-station strong {
        color: var(--czvr-text);
        font-weight: 500;
        text-transform: uppercase;
    }

    .feedback-wall-empty {
        background: var(--czvr-surface-muted);
        border: 1px solid var(--czvr-border);
        border-radius: 0.7rem;
        color: var(--czvr-text-muted);
        padding: 3rem 1.5rem;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .feedback-wall-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
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
