@extends('layouts.master')

@section('title', 'News - Vancouver FIR')
@section('description', 'News from Vancouver FIR')

@push('styles')
    <link href="{{ asset('css/pages/public.css') }}" rel="stylesheet">
@endpush

@section('content')
    <main class="container public-listing-page py-4">
        <div class="public-listing-header">
            <h1 class="blue-text">News</h1>
        </div>

        <div class="news-index-list">
            @forelse($news as $n)
                <article class="news-index-card">
                    <a href="{{ route('news.articlepublic', $n->slug) }}" class="news-index-image {{ $n->image ? '' : 'blue' }}" aria-label="Read {{ $n->title }}">
                        @if ($n->image)
                            <img src="{{ $n->image }}" alt="" loading="lazy">
                        @endif
                    </a>
                    <div class="news-index-content">
                        <a class="news-index-title" href="{{ route('news.articlepublic', $n->slug) }}">{{ $n->title }}</a>
                        @if ($n->content)
                            <div class="news-index-summary">{{ $n->toHtml('content') }}</div>
                        @endif
                        <div class="news-index-meta">
                            <span @if($n->edited) title="Last edited {{ $n->edited_pretty() }}" @endif>
                                <i class="far fa-clock" aria-hidden="true"></i> Published {{ $n->published_pretty() }}
                            </span>
                            <span><i class="far fa-user-circle" aria-hidden="true"></i> {{ $n->author_pretty() }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="public-listing-empty">No news has been published yet!</div>
            @endforelse
        </div>
    </main>
@stop
