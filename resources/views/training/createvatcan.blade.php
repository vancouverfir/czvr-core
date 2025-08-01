@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <a href="{{ route('training.students.view', $student->id) }}" class="blue-text" style="font-size: 1.2em;">
        <i class="fas fa-arrow-left"></i> Student
    </a>

    <div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">New Vatcan Note for {{ $student->user->fullName('FLC') }}</h1>

        <form method="POST" action="{{ route('vatcan.notes.new', $student->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" name="position" id="position" class="form-control" required>
            </div>
            <hr>
            <div class="form-group">
                <label for="note_content">Note Content</label>
                <textarea name="note_content" id="note_content" class="form-control" rows="3" required></textarea>
                <small>Try and include as much detail as possible. What does your student need to work on, what did you talk about/practice during the session?</small>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" name="visiting_controller_note" id="visiting_controller_note" class="form-check-input" value="1">
                <label for="visiting_controller_note" class="form-check-label">Visiting Controller Note</label><br>
                <small>If you toggle the switch above, it will set the facility that this training note belongs to as the one the instructor is from!</small>
            </div>
            <hr>
            <div class="form-group">
                <label for="session_type">Session Type</label>
                <select name="session_type" id="session_type" class="form-control" required>
                    <option value="">Select</option>
                    <option value="0">Sweatbox</option>
                    <option value="1">OJT (Monitoring)</option>
                    <option value="2">OTS</option>
                    <option value="3">Generic</option>
                </select>
            </div>
            <hr>
            <div id="ots_fields" style="display:none;">
                <div class="form-group">
                    <label for="ots_file">Select File (for OTS)</label>
                    <input type="file" name="ots_file" id="ots_file" class="form-control-file">
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="ots_passed" id="ots_passed" class="form-check-input" value="1">
                    <label for="ots_passed" class="form-check-label">OTS Passed?</label>
                </div>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" name="backdate_training_note" id="backdate_training_note" class="form-check-input" value="1">
                <label for="backdate_training_note" class="form-check-label">Backdate Training Note?</label>
            </div>

            <div class="form-group" id="backdate_date_field" style="display: none;">
                <label for="backdate_date">Select Backdate</label>
                <input type="datetime-local" name="backdate_date" id="backdate_date" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Create Note</button>
        </form>
    </div>
</div>

<script>
    const sessionTypeSelect = document.getElementById('session_type');
    const otsFields = document.getElementById('ots_fields');
    const otsFileInput = document.getElementById('ots_file');
    const otsPassedCheckbox = document.getElementById('ots_passed');

    sessionTypeSelect.addEventListener('change', function() {
        if (this.value === '2') {
            otsFields.style.display = 'block';
            otsFileInput.setAttribute('required', 'required');
        } else {
            otsFields.style.display = 'none';
            otsFileInput.removeAttribute('required');
            otsFileInput.value = null;
            otsPassedCheckbox.checked = false;
        }
    });

    sessionTypeSelect.dispatchEvent(new Event('change'));

    const backdateCheckbox = document.getElementById('backdate_training_note');
    const backdateDateField = document.getElementById('backdate_date_field');

    backdateCheckbox.addEventListener('change', function () {
        if (this.checked) {
            backdateDateField.style.display = 'block';
            document.getElementById('backdate_date').setAttribute('required', 'required');
        } else {
            backdateDateField.style.display = 'none';
            document.getElementById('backdate_date').removeAttribute('required');
            document.getElementById('backdate_date').value = '';
        }
    });

    backdateCheckbox.dispatchEvent(new Event('change'));

    document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn.disabled) {
        e.preventDefault();
    } else {
        submitBtn.disabled = true;
    }
    });
</script>

@stop
