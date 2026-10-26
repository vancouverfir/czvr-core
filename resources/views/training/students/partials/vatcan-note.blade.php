@php
    $sessionTypes = \App\Services\VatcanClient::SESSION_TYPES;
    $sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
    $sessionType = $note['session_type'] ?? 3;
@endphp
<div class="border rounded p-3 mb-3">
    <div class="d-flex align-items-start">
        <i class="far fa-sticky-note me-3"></i>
        <div class="flex-grow-1">
            <h6 class="mb-1">{{ $note['instructor_name'] ?? 'Unknown' }} - {{ \App\Support\VatcanFacilities::name(isset($note['facility_id']) ? (int) $note['facility_id'] : null) ?? 'Unknown' }} {{ \Carbon\Carbon::parse($note['friendly_time'] ?? $note['created_at'] ?? now())->format('F j, Y') }}</h6>
            <div class="mt-1">
                @if (! empty($note['position_trained']))
                    <span class="badge bg-info">{{ $note['position_trained'] }}</span>
                @endif
                <span class="badge {{ $sessionBadgeColors[$sessionType] ?? 'bg-info' }}">{{ $sessionTypes[$sessionType] ?? 'Generic' }}</span>
            </div>
            @if (($editableVatcanNotes ?? false) && \App\Http\Controllers\AtcTraining\VatcanController::canEditNote($note))
                <button type="button" class="btn btn-link btn-sm p-0 mt-2" data-bs-toggle="modal" data-bs-target="#vatcanNoteModal"
                    data-vatcan-note-edit="{{ route('training.students.vatcan.notes.update', [$student, $note['id']]) }}"
                    data-position="{{ $note['position_trained'] ?? '' }}" data-session-type="{{ $sessionType }}" data-note="{{ $note['training_note'] ?? '' }}">
                    <i class="fas fa-pen me-1"></i>Edit (within 24 hours)
                </button>
            @endif
            <p class="mt-3 mb-0" style="white-space: pre-wrap;">{{ $note['training_note'] ?? 'No written training note was supplied!' }}</p>
            @if (! empty($note['marking_sheet']))
                <hr class="my-3"><a href="{{ $note['marking_sheet'] }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm"><i class="far fa-list-alt me-1"></i> View Marking Sheet</a>
            @endif
        </div>
    </div>
</div>
