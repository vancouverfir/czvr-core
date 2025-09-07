@extends('errors.minimal')

@section('title', __('Service Unavailable'))

@section('message')
<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; text-align: center; font-family: 'Nunito', sans-serif; color: #333;">
    <h1 style="font-size: 8rem; margin: 0;">503</h1>
    <h2 style="font-size: 2rem; margin: 0.5rem 0;">Service Unavailable</h2>
    <p style="font-size: 1.2rem; max-width: 400px;">
        Sorry! We're performing some maintenance at the moment. We'll be back shortly.
    </p>
    <p style="margin-top: 1rem; font-size: 1rem; color: #666;">
        Estimated time: <span id="countdown">100</span> seconds
    </p>
</div>

<script>
    let countdown = 100;
    const el = document.getElementById('countdown');
    const interval = setInterval(() => {
        countdown--;
        el.textContent = countdown;
        if(countdown <= 0) clearInterval(interval);
    }, 1000);
</script>

@endsection
