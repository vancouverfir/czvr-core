<div class="modal fade" id="vatcanNoteModal" tabindex="-1" aria-labelledby="vatcanNoteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.students.vatcan.notes.store', $student) }}" id="vatcanNoteForm"
                data-store-url="{{ route('training.students.vatcan.notes.store', $student) }}">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="vatcanNoteTitle" data-student="{{ $student->user->fullName('FLC') }}">Add Training Note for {{ $student->user->fullName('FLC') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="vatcanNotePosition" class="form-label">Position</label>
                    <input type="text" id="vatcanNotePosition" name="position" class="form-control border rounded px-2 mb-3" maxlength="10" placeholder="CYVR_TWR" list="vatcanNotePositions" autocomplete="off" style="text-transform: uppercase;" value="{{ old('position') }}" required>
                    <datalist id="vatcanNotePositions">
                        @foreach (\App\Models\Network\MonitoredPosition::query()->orderBy('identifier')->pluck('identifier') as $identifier)
                            <option value="{{ $identifier }}"></option>
                        @endforeach
                    </datalist>

                    <label for="vatcanNoteSession" class="form-label">Session Type</label>
                    <select id="vatcanNoteSession" name="session_type" class="form-select no-custom-select border rounded px-2 mb-3" required>
                        @foreach (\App\Services\VatcanClient::SESSION_TYPES as $id => $label)
                            <option value="{{ $id }}" @selected((string) old('session_type', '0') === (string) $id)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <label for="vatcanNoteText" class="form-label">Training Note</label>
                    <textarea id="vatcanNoteText" name="note" class="form-control border rounded px-2" rows="4" minlength="3" maxlength="5000" placeholder="Try and include as much detail as possible! What does your student need to work on, what did you talk about or practice during the session?" required>{{ old('note') }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Create Training Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('vatcanNoteForm');
        const title = form.querySelector('#vatcanNoteTitle');
        const position = form.querySelector('#vatcanNotePosition');

        position.addEventListener('input', () => { position.value = position.value.toUpperCase(); });

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-vatcan-note-new], [data-vatcan-note-edit]');

            if (!trigger) {
                return;
            }

            const editing = trigger.hasAttribute('data-vatcan-note-edit');
            form.action = editing ? trigger.dataset.vatcanNoteEdit : form.dataset.storeUrl;
            form.querySelector('[name="_method"]').value = editing ? 'PATCH' : 'POST';
            title.textContent = (editing ? 'Edit Training Note for ' : 'Add Training Note for ') + title.dataset.student;
            form.querySelector('button[type="submit"]').textContent = editing ? 'Update Training Note' : 'Create Training Note';
            position.value = editing ? trigger.dataset.position : '';
            form.querySelector('#vatcanNoteSession').value = editing ? trigger.dataset.sessionType : '0';
            form.querySelector('#vatcanNoteText').value = editing ? trigger.dataset.note : '';
        });
    }());
</script>
