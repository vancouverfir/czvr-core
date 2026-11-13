@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Manage ATC Resources')
@section('description', 'Manage resources for Vancouver controllers.')

@section('content')
@include('includes.trainingMenu')

@php
    $resourceIcons = [
        'fa-cloud' => 'Weather',
        'fa-headset' => 'ATC',
        'fa-plane-departure' => 'NOTAM',
        'fa-book' => 'Document',
        'fa-map' => 'Map',
        'fa-comments' => 'Chat',
        'fa-graduation-cap' => 'Training',
        'fa-exclamation-triangle' => 'Alert',
        'fa-info-circle' => 'Information',
        'fa-video' => 'Video',
        'fa-file-alt' => 'Other',
    ];
@endphp

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <a href="{{ route('training.resources') }}" class="small"><i class="fas fa-arrow-left me-1"></i>ATC Resources</a>
            <h1 class="blue-text mt-2">Manage Resources</h1>
            <p>Every resource here is available only through the controller Training Centre.</p>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
            <i class="fas fa-plus me-1"></i>Add resource
        </button>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="training-surface">
        @forelse ($resources as $resource)
            <article class="resource-manager-row">
                <span class="resource-manager-icon"><i class="fas {{ $resource->font_awesome }}" aria-hidden="true"></i></span>
                <div class="resource-manager-copy">
                    <strong>{{ $resource->resolvedTitle() }}</strong>
                    <span>{{ $resource->resolvedDescription() }}</span>
                </div>
                <div class="resource-manager-actions">
                    <a href="{{ $resource->resolvedUrl() }}" target="_blank" rel="noopener" class="btn btn-sm btn-light" title="Open resource">
                        <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editResource{{ $resource->id }}">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('atcresources.delete', $resource->id) }}" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($resource->title) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </article>

            <div class="modal fade" id="editResource{{ $resource->id }}" tabindex="-1" aria-labelledby="editResource{{ $resource->id }}Title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('atcresources.update', $resource) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="resource_form" value="edit-{{ $resource->id }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editResource{{ $resource->id }}Title">Edit Resource</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('includes.atcResourceFields', ['resource' => $resource, 'resourceIcons' => $resourceIcons, 'fieldPrefix' => 'edit-'.$resource->id])
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">No ATC resources have been added yet.</div>
        @endforelse
    </div>
</main>

<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('atcresources.upload') }}">
                @csrf
                <input type="hidden" name="resource_form" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addResourceModalTitle">Add Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('includes.atcResourceFields', ['resource' => null, 'resourceIcons' => $resourceIcons, 'fieldPrefix' => 'add'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Add resource</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
    (() => {
        document.querySelectorAll('.resource-icon-select').forEach((select) => {
            const preview = document.getElementById(select.dataset.preview);

            const updatePreview = () => {
                if (preview) {
                    preview.className = `fas ${select.value}`;
                }
            };

            select.addEventListener('change', updatePreview);

            if (window.jQuery) {
                window.jQuery(select).on('change.resourceIcon select2:select.resourceIcon', updatePreview);
            }

            updatePreview();
        });

        @if ($errors->any())
            const failedForm = @json(old('resource_form'));
            const modalId = failedForm === 'add' ? 'addResourceModal' : `editResource${failedForm?.replace('edit-', '')}`;
            const modal = document.getElementById(modalId);

            if (modal && window.bootstrap?.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        @endif
    })();
</script>
@endpush
