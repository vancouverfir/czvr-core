{{-- Sortable/searchable tables, loaded only on pages that include it --}}
@once
    @push('vendor-styles')
        <link href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    @endpush
    @push('vendor')
        <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
    @endpush
@endonce
