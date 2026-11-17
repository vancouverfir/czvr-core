@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Checklists - Vancouver FIR')

@section('content')
@include('includes.trainingMenu')

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <a href="{{ route('training.students.students') }}" class="small"><i class="fas fa-arrow-left me-1"></i>Students</a>
            <h1 class="blue-text mt-2">Checklists</h1>
            <p>Create and edit checklists or delete one!</p>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addChecklistModal">
            <i class="fas fa-plus me-1"></i>Add checklist
        </button>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    @foreach ($checklists as $checklist)
        @php
            $labels = collect($homeMaps->get($checklist->id, []))->map(fn ($map) => $map->label?->name.' '.$map->tier_type)
                ->merge(collect($visitorMaps->get($checklist->id, []))->map(fn ($map) => $map->label?->name.' '.$map->tier_type.' (visitor)'));
        @endphp
        <details class="training-panel checklist-editor mb-3">
            <summary class="training-panel-header">
                <h2>{{ $checklist->name }}</h2>
                <span class="checklist-editor-meta">
                    @foreach ($labels as $label)
                        <span class="badge text-bg-light border">{{ $label }}</span>
                    @endforeach
                    <span class="training-panel-count">{{ $checklist->items->count() }} {{ Str::plural('item', $checklist->items->count()) }}</span>
                </span>
            </summary>

            <div class="checklist-editor-row">
                <form method="POST" action="{{ route('training.checklists.update', $checklist) }}" class="checklist-editor-form">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $checklist->name }}" class="form-control form-control-sm" aria-label="Checklist name" required>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Rename</button>
                </form>
                @if ($labels->isEmpty())
                    <form method="POST" action="{{ route('training.checklists.destroy', $checklist) }}" onsubmit="return confirm('Delete this checklist? Students lose their progress on it.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete checklist</button>
                    </form>
                @else
                    <button type="button" class="btn btn-sm btn-outline-danger" disabled title="Assigned by a label, so it can't be deleted">Delete checklist</button>
                @endif
            </div>

            @foreach ($checklist->items as $item)
                <div class="checklist-editor-row">
                    <form method="POST" action="{{ route('training.checklists.items.update', $item) }}" class="checklist-editor-form">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="item" value="{{ $item->item }}" class="form-control form-control-sm" aria-label="Checklist item" required>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                    </form>
                    <form method="POST" action="{{ route('training.checklists.items.destroy', $item) }}" onsubmit="return confirm('Delete this item? Students lose their progress on it.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete item"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            @endforeach

            <div class="checklist-editor-row checklist-editor-add">
                <form method="POST" action="{{ route('training.checklists.items.store', $checklist) }}" class="checklist-editor-form">
                    @csrf
                    <input type="text" name="item" class="form-control form-control-sm" placeholder="New item" aria-label="New item" required>
                    <button type="submit" class="btn btn-sm btn-success text-nowrap">Add item</button>
                </form>
                <small class="blue-text">New items are also added for every student who already has this checklist!</small>
            </div>
        </details>
    @endforeach
</main>

<div class="modal fade" id="addChecklistModal" tabindex="-1" aria-labelledby="addChecklistTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.checklists.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addChecklistTitle">Add Checklist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="newChecklistName" class="form-label">Name</label>
                    <input type="text" name="name" id="newChecklistName" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Add checklist</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
