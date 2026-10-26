@extends('layouts.master')
@section('title', 'Meeting Minutes - Vancouver FIR')
@section('description', 'View Vancouver FIR staff meeting minutes')

@section('content')
<main class="container py-4">
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
        <div>
            <h1 class="blue-text">Meeting Minutes</h1>
            <p>Minutes from Vancouver FIR staff meetings!</p>
        </div>
        @can('meeting-minutes.manage')
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upload"><i class="fas fa-upload me-1"></i>Upload Minutes</button>
        @endcan
    </div>

    <div class="page-list">
        @forelse ($minutes as $m)
            <div class="page-list-item is-link">
                <span>
                    <strong><a class="stretched-link" target="_blank" rel="noopener noreferrer" href="{{ $m->link }}">{{ $m->title }}</a></strong>
                    <small>
                        {{ $m->created_at?->format('d/m/Y') }}
                        @can('meeting-minutes.manage') · Added by {{ $m->user?->fullName('FLC') ?? 'Unknown' }} @endcan
                    </small>
                </span>
                <i class="fas fa-arrow-up-right-from-square page-list-open" aria-hidden="true"></i>
                @can('meeting-minutes.manage')
                    <span class="page-list-actions">
                        <form method="POST" action="{{ route('meetingminutes.delete', $m->id) }}" onsubmit="return confirm('Delete {{ addslashes($m->title) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </span>
                @endcan
            </div>
        @empty
            <div class="page-list-item text-muted">No meeting minutes available!</div>
        @endforelse
    </div>
</main>

@can('meeting-minutes.manage')
<div class="modal fade" id="upload" tabindex="-1" aria-labelledby="uploadTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('meetingminutes.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadTitle">Upload Minutes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="minutesTitle" class="form-label">Title</label>
                    <input type="text" id="minutesTitle" name="title" class="form-control mb-3" required>
                    <label for="minutesFile" class="form-label">File</label>
                    <input type="file" id="minutesFile" name="file" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Upload</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@stop
