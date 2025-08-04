@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <!-- Instructors -->
    @include('includes.trainingMenu')
    @if (Auth::check() && Auth::user()->permissions >= 2)
    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <h2 class="font-weight-bold blue-text">Instructor Portal</h2>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Your Students</text>
                    </div>
                    <div class="card-body">
                        @if ($yourStudents !== null && count($yourStudents) > 0)
                        <div class="list-group">
                            @foreach ($yourStudents as $student)
                            <a href="{{route('training.students.view', $student->id)}}" class="list-group-item d-flex justify-content-between align-items-center"> <img src="{{$student->user->avatar()}}" style="height: 30px; width: 30px; margin-right: 15px; border-radius: 50%;">
                                {{$student->user->fullName('FLC')}}
                                {{-- <i class="text-dark">Session planned at {date}</i> --}}
                                @if ($student->status == 4)
                                    <span class="btn-sm btn-danger">
                                        <h6 class="p-0 m-0">On Hold</h6>
                                    </span>
                                @else
                                    <span class="btn-sm btn-success">
                                        <h6 class="p-0 m-0">Open</h6>
                                    </span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        @else
                            No students are allocated to you!
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Upcoming Sessions</text>
                    </div>

                    <div class="card-body">
                        // Insert code here
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif (auth()->user()->studentProfile)

    <style>
        #training-notes-container {
        max-height: 545px;
        overflow-y: auto;
        }
    </style>

    <!-- User -->
    <div class="container" style="margin-top: 20px; margin-bottom: 30px;">
        <div class="row">
            <div class="col">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-row w-100 align-items-center h-100">
                            <img src="{{$student->user->avatar()}}" style="height: 50px; width: 50px; margin-right: 15px; border-radius: 50%;">
                            <div class="d-flex flex-column w-100">
                                <h5 class="list-group-item">
                                    {{$student->user->fullName('FLC')}} - {{$student->user->rating_short}} {{$student->user->division_code}} {{$student->user->region_code}} {{$student->user->subdivision_code}}
                                    <div class="d-flex flex-wrap mt-2 justify-content-left align-items-left">
                                        @foreach($student->labels as $label)
                                            <span class="mr-2 mb-1" style="background-color: {{$label->label->color}};">
                                                {{$label->label->labelHtml()}}
                                            </span>
                                        @endforeach
                                    </div>
                                </h5>
                            </div>
                        </div>
                        <h5 class="mt-3 font-weight-bold">Instructor</h5>
                        @if ($student->instructor)
                            <div class="d-flex flex-column w-50 align-items-left">
                                <h5 class="list-group-item" style="background-color: transparent; border: none;">
                                    <img src="{{$student->instructor->user->avatar()}}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;"> {{$student->instructor->user->fullName('FLC')}}
                                </h5>
                            </div>
                        @else
                            <span>No Instructor Assigned!</span>
                        @endif

                        @if (in_array($student->status, [0, 3]))
                            <h5 class="mt-3 font-weight-bold">Availability</h5>
                            <span>{{$student->times ?? 'Not yet submitted!'}}</span>
                        @endif
                    </div>
                </div>

        <!-- Checklist -->
            <div class="card mb-3" style="max-height: 700px; overflow-y: auto;">
                <div class="card-body">
                    @if ($studentChecklistGroups->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3 sticky-top p-2" style="background-color: #2e2f2f;">
                            <h3 class="font-weight-bold blue-text mb-0">Your Checklists</h3>
                            <div class="d-flex align-items-center gap-2">
                                <button id="toggle-completed" class="btn btn-sm btn-outline-info">
                                    Hide Completed
                                </button>
                            </div>
                        </div>
                        @foreach ($studentChecklistGroups as $checklistName => $items)
                        <div class="mb-3 border p-3 rounded">
                            <div class="d-flex justify-content-center mb-3" style="min-height: 40px;">
                                <h3 class="font-weight-bold mb-0">{{ $checklistName }}</h3>
                            </div>
                            <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" id="checklist-form-{{ Str::slug($checklistName) }}">
                                @csrf
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                        <tr class="{{ $item->completed ? 'completed-row' : '' }}" style="{{ $item->completed ? 'text-decoration: line-through; color: grey' : '' }}">
                                            <td>{{ $item->checklistItem->item }}</td>
                                            <td class="text-center">
                                                @if ($item->completed)
                                                    Completed
                                                @else
                                                    Not Completed
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        @endforeach
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-weight-bold blue-text mb-0">Your Checklist</h3>
                        </div>
                        <span style="align-items:center;">No checklists assigned!</span>
                    @endif
                </div>
            </div>
        </div>

    <!-- Info -->
    <div class="col">
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="font-weight-bold blue-text text-primary mb-3">Info</h3>

                @if ($student->status == 3)
                    <p>There are currently <strong>{{ $Visitors }}</strong> visitors total!</p>

                @elseif ($student->status == 0)
                    @if ($waitlistPosition)
                        <p>You are currently <strong>{{ $waitlistPosition }}</strong> on the waitlist!</p>
                    @else
                        <p>You are currently not on the waitlist!</p>
                    @endif

                    <button class="btn btn-sm btn-outline-info mt-2 mb-3" id="editTimes">Edit Availability</button>

                    <small class="d-block mb-3"> You last renewed your training request {{ $student->renewed_at?->format('F j, Y H:i') ?? 'not renewed yet' }} </small>

                    <hr>

                    <p><strong>Estimated Wait Time:</strong> {{ $training_time->wait_length }}</p>

                @else
                    <h3>Your Training has Started!</h3>
                @endif

                <div id="timesFormContainer" class="mt-3" style="display:none;">
                    <form method="POST" action="{{ route('training.students.editTimes', $student->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="timesInput">Session Times</label>
                            <textarea name="times" id="timesInput" rows="3" class="form-control" placeholder="Enter your availability here!"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="cancelTimesBtn">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vatcan Notes -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="font-weight-bold blue-text mb-0">Latest Vatcan Notes</h3>
                    @csrf
                    <div>
                        <a href="{{ route('training.students.allnotes', $student->id) }}" class="text-primary btn btn-sm btn-outline-info ms-2">
                            View All
                        </a>
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    Notes left by non Vancouver FIR members not shown!
                </div>
            </div>
            <div class="card-body" id="training-notes-container">
                <span>Loading latest Vatcan Notes!</span>
            </div>
        </div>
    </div>
</div>

    <script>
    $(document).ready(function() {
        $('.label-card').click(function() {
            $('.label-card').removeClass('selected').css('border', '1px solid transparent');
            $(this).addClass('selected');
            const labelId = $(this).data('id');
            $('#selectedLabel').val(labelId);
            $(this).css('border', '1px solid #ffffff');
        });
    });

    document.getElementById('editTimes').addEventListener('click', function() {
        document.getElementById('timesFormContainer').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelTimesBtn').addEventListener('click', function() {
        document.getElementById('timesFormContainer').style.display = 'none';
        document.getElementById('editTimes').style.display = 'inline-block';
    });

    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('toggle-completed');
        let hidden = false;

        if (button) {
            button.addEventListener('click', function () {
                const rows = document.querySelectorAll('.completed-row');
                rows.forEach(row => row.style.display = hidden ? '' : 'none');
                hidden = !hidden;
                button.textContent = hidden ? 'Show Completed' : 'Hide Completed';
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const studentId = {{ $student->id }};

        fetch(`/api/training-notes?student_id=${studentId}`)
            .then(response => response.json())
            .then(notes => {
                const container = document.getElementById('training-notes-container');
                container.innerHTML = '';

                const trainingNotesCard = container.closest('.card');

                if (!notes.length) {
                    container.innerHTML = '<span class="alert">You have no training notes available!</span>';
                    if (trainingNotesCard) {
                        trainingNotesCard.style.marginBottom = '30px';
                    }
                } else {
                    if (trainingNotesCard) {
                        trainingNotesCard.style.marginBottom = '';
                    }
                }

                const sessionTypes = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];
                const sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
                const afacility = ['Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR', 'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'];

                notes.slice(0, 3).forEach(note => {
                    const noteHtml = `
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="far fa-sticky-note mr-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${note.instructor_name} - ${afacility[note.facility_id]} ${new Date(note.friendly_time).toLocaleDateString(undefined, {year:'numeric', month:'long', day:'numeric'})}</h6>
                                    <div class="mt-1">
                                        <span class="badge bg-info">${note.position_trained}</span>
                                        <span class="badge ${sessionBadgeColors[note.session_type]}">${sessionTypes[note.session_type]}</span>
                                    </div>
                                    <p class="mt-3 mb-0" style="white-space: pre-wrap;">${note.training_note}</p>
                                    ${note.marking_sheet ? `<hr class="my-3"><a href="${note.marking_sheet}" target="_blank" class="btn btn-outline-info btn-sm"><i class="far fa-list-alt me-1"></i> View Marking Sheet</a>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', noteHtml);
                });
            })
            .catch(err => {
                console.error('Failed to load training notes:', err);
                document.getElementById('training-notes-container').innerHTML = '<div>Failed to load training notes!</div>';
        });
    });
    </script>

    @else
    {{ abort(403) }}
    @endif
@stop
