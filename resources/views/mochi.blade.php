@extends('layouts.master')
@section('title', 'You found Mochi!')

@push('styles')
<style>
.mochi-stage { position: relative; width: min(100%, 420px); margin: 1.5rem 0 1.25rem; }
.mochi-stage button { display: block; width: 100%; padding: 0; overflow: hidden; border: 0; border-radius: .75rem; background: none; cursor: pointer; }
.mochi-stage img { display: block; width: 100%; height: auto; transition: transform .2s ease; }
.mochi-stage button:hover img { transform: scale(1.02); }
.mochi-stage.is-chirping img { animation: mochi-hop .35s ease; }
.mochi-chirp { position: absolute; top: -.9rem; right: -.5rem; padding: .35rem .8rem; border-radius: 1rem 1rem 1rem .2rem; color: var(--czvr-white); background: var(--czvr-green); font-weight: 500; opacity: 0; transform: translateY(.4rem) scale(.9); transition: opacity .2s ease, transform .2s ease; pointer-events: none; }
.mochi-stage.is-chirping .mochi-chirp { opacity: 1; transform: none; }
@keyframes mochi-hop { 40% { transform: translateY(-6%); } }
@media (prefers-reduced-motion: reduce) {
    .mochi-stage img, .mochi-chirp { transition: none; }
    .mochi-stage.is-chirping img { animation: none; }
}
</style>
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
