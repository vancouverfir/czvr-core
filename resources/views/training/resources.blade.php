@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <h1 class="blue-text">ATC Resources</h1>
            <p>Training material, operational references, and controller tools!</p>
        </div>
        @can('atc-resources.manage')
            <a href="{{ route('atcresources.index') }}" class="btn btn-sm btn-outline-primary">Manage resources</a>
        @endcan
    </header>

    <div class="training-surface mb-3">
        <a href="https://vatcan.ca/my/cbt/CZVR" target="_blank" rel="noopener" class="training-cbt-link">
            <span><i class="fas fa-graduation-cap me-2" aria-hidden="true"></i>Vancouver FIR courses on VATCAN</span>
            <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>

    <div class="training-surface">
        <div class="training-resource-grid p-3">
            @forelse($atcResources as $resource)
                <a href="{{ $resource->resolvedUrl() }}" target="_blank" rel="noopener" class="training-resource">
                    <i class="fas {{ $resource->font_awesome }} training-resource-icon" aria-hidden="true"></i>
                    <h2>{{ $resource->resolvedTitle() }}</h2>
                    <p>{{ $resource->resolvedDescription() }}</p>
                    <span class="training-resource-open">Open resource <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></span>
                </a>
            @empty
                <p class="text-muted mb-0 p-2">No resources are currently available.</p>
            @endforelse
        </div>
    </div>
</main>

@stop
