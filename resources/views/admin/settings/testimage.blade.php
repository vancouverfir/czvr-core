@extends('layouts.master')

@section('title', 'Homepage Image Preview')

@push('styles')
    <link href="{{ asset('css/pages/homepage.css') }}" rel="stylesheet">
@endpush

@section('content')
    <main>
        <div class="home-landing">
            <section class="home-hero">
                <img class="homepage-background is-active" src="{{ $image->browserUrl() }}" alt="" fetchpriority="high" draggable="false">
                <div class="container homepage-hero-inner">
                    <div class="homepage-hero-copy">
                        <span class="homepage-hero-leaf" aria-hidden="true"><i class="fab fa-canadian-maple-leaf"></i></span>
                        <h1>From Sea to Sky!</h1>
                        <p>Discover virtual aviation across Canada’s spectacular West Coast!</p>
                        <a class="home-explore-link" href="{{ route('settings.images') }}">
                            Back to homepage images <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <span class="home-inline-credit">Screenshot by {{ $image->credit }}</span>
            </section>
        </div>
    </main>
@stop
