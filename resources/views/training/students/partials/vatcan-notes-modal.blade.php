<div class="modal fade" id="allVatcanNotes" tabindex="-1" aria-labelledby="allVatcanNotesTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="allVatcanNotesTitle">VATCAN Notes</h5>
                    <div class="small text-muted">{{ count($vatcanNotes) }} total · Notes from outside Vancouver FIR are hidden!</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @forelse ($vatcanNotes as $note)
                    @include('training.students.partials.vatcan-note', ['note' => $note])
                @empty
                    <p class="text-muted mb-0">No training notes yet! Check back later!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
