{{-- Searchable dropdowns, for pages that call select2() themselves; form-controls.js loads it on demand otherwise --}}
@once
    @push('vendor-styles')
        <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet">
    @endpush
    @push('vendor')
        <script src="{{ asset('vendor/select2/select2.full.min.js') }}"></script>
    @endpush
@endonce
