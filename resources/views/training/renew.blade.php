@extends('layouts.master')

@section('title', 'Renew Training - Vancouver FIR')
@section('description', 'Confirm that you want to keep your Vancouver FIR training request active!')

@push('styles')
    <link href="{{ asset('css/pages/training-availability.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container py-5">
    <div class="card mx-auto training-renewal-card">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold blue-text">Renew Your Waitlist Request</h1>
            <p class="mt-3">
                Confirm that you are still interested in Vancouver FIR training! This keeps your current waitlist position active!
            </p>
            <p class="text-muted">
                This one-time renewal must be confirmed by {{ $student->renewalDeadlineAt()?->format('F j, Y H:i') }} UTC!
            </p>

            <form method="POST" action="{{ route('training.renew.confirm', $token) }}">
                @csrf
                <div class="my-4">
                    @include('training.students.partials.availability-fields', ['availabilityEditorId' => 'renewalAvailability'])
                </div>
                <button type="submit" class="btn btn-success">Update availability and renew!</button>
                <a href="{{ route('training.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/training-availability.js') }}"></script>
@endpush
