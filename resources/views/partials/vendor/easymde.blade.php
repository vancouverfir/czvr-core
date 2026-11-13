{{-- Markdown editor, loaded only on pages that include it --}}
@once
    @push('vendor-styles')
        <link href="{{ asset('vendor/easymde/easymde.min.css') }}" rel="stylesheet">
    @endpush
    @push('vendor')
        <script src="{{ asset('vendor/easymde/easymde.min.js') }}"></script>
    @endpush
@endonce
