@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
@include('partials.vendor.easymde')
    @include('includes.trainingMenu')
    <main class="container training-page">
        <header class="training-page-header">
            <div>
                <a href="{{route('training.students.view', $student->id)}}" class="small"><i class="fas fa-arrow-left me-1"></i>Student</a>
                <h1 class="blue-text mt-2">New Staff Comment</h1>
                <p>{{ $student->user->fullName('FLC') }}</p>
            </div>
        </header>
        <div class="training-surface training-form-shell">
            <form method="POST" action="{{ route('add.trainingnote', $student->id) }}" class="form-group">
                @csrf

                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" maxlength="255" required>

                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control">{{ old('content') }}</textarea><br>

                <input type="submit" class="btn btn-success" value="Add Staff Comment">
            </form>
        </div>
    </main>
@stop

@push('scripts')
<script>
    const staffCommentEditor = document.getElementById('content');

    if (staffCommentEditor && typeof EasyMDE !== 'undefined') {
        new EasyMDE({ spellChecker: false, element: staffCommentEditor, forceSync: true });
    }
</script>
@endpush
