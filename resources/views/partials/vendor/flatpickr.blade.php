{{-- Date and time pickers, for pages that call flatpickr() themselves; form-controls.js loads it on demand otherwise --}}
@once
    @push('vendor-styles')
        <link href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    @endpush
    @push('vendor')
        <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
    @endpush
@endonce
