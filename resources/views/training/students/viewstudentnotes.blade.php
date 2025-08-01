@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

@include('includes.trainingMenu')

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <a class="blue-text" href="{{ route('training.students.view', $student->id) }}" style="font-size: 1.2em;">
        <i class="fas fa-arrow-left"></i> Student
    </a>

    <hr>

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="font-weight-bold blue-text mb-0">{{ $student->user->fullName('FLC') }}'s Vatcan Notes</h1>
                    <p class="text-muted small mb-0" id="training-notes-count">Total ? </p>
                </div>
                <div class="small mt-1">Notes left by non Vancouver FIR members not shown!</div>
            </div>
        </div>

        <div class="card-body">
            <div class="list-group" id="training-notes-container">
                <p>Loading Vatcan Notes!</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentId = {{ $student->id }};

    fetch(`/api/training-notes?student_id=${studentId}`)
        .then(res => res.json())
        .then(notes => {
            const container = document.getElementById('training-notes-container');
            const countElem = document.getElementById('training-notes-count');

            container.innerHTML = '';
            countElem.textContent = `Total ${notes.length}`;

            if (!notes.length) {
                container.innerHTML = '<div>No Vatcan Notes available!</div>';
                return;
            }

            const sessionTypes = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];
            const sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
            const afacility = ['Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR', 'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'];

            notes.forEach(note => {
                const noteHtml = `
                    <div class="list-group-item shadow-sm mb-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="far fa-comment-alt fa-lg text-primary mr-3"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 7px;">
                                        <span class="fw-bold">
                                            ${note.instructor_name} - ${afacility[note.facility_id]}
                                        </span>

                                        <span class="text-muted">
                                            ${new Date(note.friendly_time).toLocaleDateString(undefined, {year:'numeric', month:'long', day:'numeric'})}
                                        </span>

                                        <span class="badge bg-primary">
                                            ${note.position_trained}
                                        </span>

                                        <span class="badge ${sessionBadgeColors[note.session_type]}">
                                            ${sessionTypes[note.session_type]}
                                        </span>
                                    </div>
                                </div>

                                <span style="white-space: pre-wrap;">${note.training_note}</span>

                                ${note.marking_sheet ? `
                                    <hr class="my-3">
                                    <a href="${note.marking_sheet}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="far fa-list-alt me-1"></i> View Marking Sheet
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', noteHtml);
            });
        })
        .catch(err => {
            console.error('Failed to load training notes', err);
            document.getElementById('training-notes-container').innerHTML = '<div class="alert alert-danger">Failed to load training notes!</div>';
        });
});
</script>
@stop
