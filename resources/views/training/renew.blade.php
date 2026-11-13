@extends('layouts.master')

@section('title', 'Renew Training - Vancouver FIR')
@section('description', 'Confirm that you want to keep your Vancouver FIR training request active!')

@push('styles')
    <link href="{{ asset('css/pages/training.css') }}" rel="stylesheet">
@endpush

@php
    $periods = $student->availabilityPeriods();
    $editing = $errors->any() || empty($periods);
@endphp

@section('content')
<main class="container py-4">
    <div class="page-heading">
        <h1 class="blue-text">Renew Your Waitlist Request</h1>
    </div>

    <p class="page-note mb-4">Please confirm by <strong>{{ $student->renewalDeadlineAt()?->format('F j, Y H:i') }} UTC</strong>, or your training request will be canceled!</p>

    <section class="renew-panel" id="renewStepInterest" @if ($errors->any()) hidden @endif>
        <h2>Still interested in Vancouver FIR training?</h2>
        <div class="renew-actions">
            <button type="button" class="btn btn-success" data-renew-next>Yes, keep my training request!</button>
            <a href="{{ route('training.index') }}" class="btn btn-light">Cancel and let my training request expire</a>
        </div>
    </section>

    <form method="POST" action="{{ route('training.renew.confirm', $token) }}" class="renew-panel" id="renewStepAvailability" @unless ($errors->any()) hidden @endunless>
        @csrf
        <h2>{{ $editing ? 'Let us known your general availability!' : 'Is this still your general availability?' }}</h2>
        

        @unless (empty($periods))
            <div id="renewAvailabilitySummary" @if ($editing) hidden @endif>
                <ul class="renew-periods">
                    @foreach ($periods as $period)
                        <li>{{ $period['days'] }}, {{ $period['start'] }} UTC – {{ $period['end'] }} UTC</li>
                    @endforeach
                </ul>
                @if (filled($student->times))
                    <p class="small text-muted mt-2 mb-0">Comment: {{ $student->times }}</p>
                @endif
            </div>
        @endunless

        <div id="renewAvailabilityEditor" class="mt-3" @unless ($editing) hidden @endunless>
            @include('training.students.partials.availability-fields', ['availabilityEditorId' => 'renewalAvailability'])
        </div>

        <div class="renew-actions">
            <button type="submit" class="btn btn-success" id="renewSubmit">{{ $editing ? 'Save and renew!' : 'Yes, renew!' }}</button>
            @unless ($editing)
                <button type="button" class="btn btn-light" data-renew-edit>Update my availability</button>
            @endunless
        </div>
    </form>
</main>

<script>
    (function () {
        const interest = document.getElementById('renewStepInterest');
        const form = document.getElementById('renewStepAvailability');

        document.querySelector('[data-renew-next]').addEventListener('click', function () {
            interest.hidden = true;
            form.hidden = false;
        });

        const edit = document.querySelector('[data-renew-edit]');
        if (edit) {
            edit.addEventListener('click', function () {
                document.getElementById('renewAvailabilitySummary').hidden = true;
                document.getElementById('renewAvailabilityEditor').hidden = false;
                form.querySelector('h2').textContent = 'When can you train?';
                document.getElementById('renewSubmit').textContent = 'Save and renew!';
                edit.hidden = true;
            });
        }
    }());
</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/training-availability.js') }}"></script>
@endpush
