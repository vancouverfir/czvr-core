{{-- Guided dashboard tour, loaded only on the dashboard --}}
@once
    @push('vendor-styles')
        <link href="{{ asset('vendor/driver/driver.css') }}" rel="stylesheet">
    @endpush
    @push('vendor')
        <script src="{{ asset('vendor/driver/driver.js.iife.js') }}"></script>
        <script src="{{ asset('js/dashboard-tour.js') }}"></script>
    @endpush
@endonce
