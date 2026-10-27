@extends('layouts.master')
@section('title', 'You found Mochi!')

@push('styles')
    <link href="{{ asset('css/pages/public.css') }}" rel="stylesheet">
@endpush

@section('content')
<main class="container py-4">
    <div class="page-heading">
        <h1 class="blue-text">Congrats! You've found an Easter Egg!</h1>
        <p>We don't really have anything to give you as a gift, but please feel free to click on Mochi below for a free chirping!</p>
    </div>

    <div class="mochi-stage" id="mochiStage">
        <button type="button" aria-label="Click Mochi for a chirp!">
            <img src="https://czvr.ca/storage/files/easteregg/1671213201.png" alt="Mochi">
        </button>
        <span class="mochi-chirp" aria-live="polite"></span>
    </div>

    <div>
        <a href="{{ route('index') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-1"></i>Take Me Home (Country Roads)</a>
    </div>
</main>

<script>
(function () {
    const stage = document.getElementById('mochiStage');
    const chirp = stage.querySelector('.mochi-chirp');
    let timer;

    stage.querySelector('button').addEventListener('click', function () {
        chirp.textContent = 'Chirp!';
        stage.classList.remove('is-chirping');
        void stage.offsetWidth;
        stage.classList.add('is-chirping');
        clearTimeout(timer);
        timer = setTimeout(() => stage.classList.remove('is-chirping'), 1400);
    });
}());
</script>
@endsection
