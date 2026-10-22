@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@push('styles')
<style>
    .training-resource-grid {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .training-resource {
        border: 1px solid var(--czvr-border);
        border-radius: 0.65rem;
        color: var(--czvr-text);
        display: flex;
        flex-direction: column;
        min-height: 170px;
        padding: 1rem;
    }

    .training-resource:hover,
    .training-resource:focus {
        background: var(--czvr-green-soft);
        border-color: #b9d2b5;
        color: var(--czvr-text);
    }

    .training-resource-icon {
        color: var(--czvr-green);
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }

    .training-resource h2 {
        font-size: var(--czvr-font-size-base);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .training-resource p {
        color: var(--czvr-text-muted);
        font-size: var(--czvr-font-size-base) !important;
        line-height: 1.5;
        margin: 0;
    }

    .training-resource-open {
        color: var(--czvr-green-dark);
        font-size: var(--czvr-font-size-base);
        margin-top: auto;
        padding-top: 1rem;
    }

    .training-cbt-link {
        align-items: center;
        background: var(--czvr-green);
        color: #fff;
        display: flex;
        font-size: var(--czvr-font-size-base);
        justify-content: space-between;
        padding: 0.85rem 1rem;
    }

    .training-cbt-link:hover,
    .training-cbt-link:focus {
        background: var(--czvr-green-dark);
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .training-resource-grid {
            grid-template-columns: 1fr;
        }

    }
</style>
@endpush

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
