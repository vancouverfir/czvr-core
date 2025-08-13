@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

<style>
    .instructor:hover {
    color: #2ca32c;
    }

    #training-notes-container {
    max-height: 545px;
    overflow-y: auto;
    }
</style>

@section('content')

@include('includes.trainingMenu')
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
                                            <a href="{{route('training.students.drop.label', [$student->id, $label->student_label_id])}}" title="Remove label">{{$label->label->labelHtml()}}</a>
                                        </span>
                                    @endforeach
                                    <a data-toggle="modal" data-target="#assignLabelModal" title="Add label"><i style="font-size: 0.7em; position: relative; top: 3px;" class="fas fa-plus"></i></a>
                                </div>
                            </h5>
                        </div>
                    </div>
                    <h5 class="mt-3 font-weight-bold">Instructor</h5>
                    @if ($student->instructor && auth()->user()->permissions >= 3)
                        <h5 class="instructor list-group-item" style="background-color: transparent; border: none; cursor: pointer;"
                            data-toggle="modal" data-target="#confirmRemoveInstructorModal">
                            <img src="{{ $student->instructor->user->avatar() }}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;">
                            {{ $student->instructor->user->fullName('FLC') }}
                        </h5>
                    @elseif (!$student->instructor && auth()->user()->permissions >= 3)
                        <h7 class="instructor" style="color: #2cb82c; cursor: pointer" data-toggle="modal" data-target="#assignInstructorModal">Assign Instructor</h7>
                    @elseif ($student->instructor)
                        <h5 class="list-group-item" style="background-color: transparent; border: none;">
                            <img src="{{ $student->instructor->user->avatar() }}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;">
                            {{ $student->instructor->user->fullName('FLC') }}
                        </h5>
                    @else
                        <span>Instructor not set!</span>
                    @endif
                    <h5 class="mt-3 font-weight-bold">Availability</h5>
                    <span>{{$student->times ?? 'Not yet submitted!'}}</span>
                </div>
            </div>

        <!-- Checklist -->
            <div class="card mb-3" style="max-height: 700px; overflow-y: auto;">
                <div class="card-body">
                    @if ($studentChecklistGroups->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3 sticky-top p-2" style="background-color: #2e2f2f;">
                            <h3 class="font-weight-bold blue-text mb-0">Student Checklist</h3>
                            <div class="d-flex align-items-center gap-2">
                                <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" class="mb-0 d-flex align-items-center">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" id="header-complete-selected" disabled>
                                        Complete Selected
                                    </button>
                                </form>
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
                                        <td>{{ $item->checklistItem->item }}
                                            <td class="text-center">
                                                @if ($item->completed)
                                                    Completed
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-success toggle-select-btn" data-item-id="{{ $item->id }}">Complete</button>
                                                @endif
                                            </td>
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
                            <h3 class="font-weight-bold blue-text mb-0">Student Checklist</h3>
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
                    <h3 class="font-weight-bold blue-text pb-2">Info</h3>
                    @php
                        $isVisitor = in_array($student->status, [3, 5]);
                        $routeName = $isVisitor ? 'training.students.promoteVisitor' : 'training.students.promote';
                        $buttonLabel = $isVisitor ? 'Promote Visitor' : 'Promote Student';

                        $currentLabel = $student->labels->pluck('label.name')->last();
                        $trainingOrder = $isVisitor 
                            ? \App\Models\AtcTraining\LabelChecklistVisitorMap::orderBy('id')->with('label')->get()->pluck('label.name')->unique()->values()->toArray()
                            : \App\Models\AtcTraining\LabelChecklistMap::orderBy('id')->with('label')->get()->pluck('label.name')->unique()->values()->toArray();

                        $lastLabel = $currentLabel === collect($trainingOrder)->last();
                        $formAction = $lastLabel 
                            ? route('training.students.completeTraining', $student->id) 
                            : route($routeName, $student->id);
                    @endphp
                    <h7 class="list-group-item" style="background: transparent; color: #2cb82c; cursor: pointer;">
                        <form id="promotionForm" method="POST" action="{{ $formAction }}">
                            @csrf
                            <button type="button" class="btn btn-link p-0" style="all: unset; cursor: pointer; color: inherit;" 
                                onclick="handlePromotionClick({{ $lastLabel ? 'true' : 'false' }})">
                                {{ $buttonLabel }}
                            </button>
                        </form>
                    </h7>
                    <h7 class="list-group-item" style="background: transparent;">
                        <form method="POST" action="{{ route('training.students.assignT2', $student->id) }}" style="display: inline;">
                            @csrf
                            <button style="all: unset; color: inherit; cursor: pointer;">Add Tier 2 Checklists</button>
                        </form>
                    </h7>
                    @if (auth()->user()->permissions >= 3)
                        <h7 class="list-group-item" style="background: transparent"><a href="{{ url('/dashboard/roster/edit/' . $student->user->id) }}" style="color: inherit;">Edit Certifications</a></h7>
                    @endif
                    <h7 class="list-group-item" style="background: transparent">Created {{$student->created_at?->format('F jS Y')}}</h7>
                    @if (Auth::user()->permissions >= 4)
                        <h7 class="list-group-item" style="background: transparent; color: #ff0000; cursor: pointer"><a href="{{ route('training.students.delete', $student->id) }} " style="color: inherit">Delete Student</a></h7>
                    @endif
                </div>
            </div>

            <!-- Vatcan Notes -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="font-weight-bold blue-text mb-0">Latest Vatcan Notes</h3>
                        @csrf
                        <div>
                            <a href="{{ url('https://vatcan.ca/manage/training/notes/controller/' . $student->user_id) }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm">
                                New Vatcan Note
                            </a>
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

        <!-- Staff Comments -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-weight-bold blue-text mb-0">Staff Comments</h3>
                            <a class="btn btn-sm btn-outline-info" href="{{ route('view.add.note', $student->id) }}">New Staff Comment</a>
                        </div>
                        <div class="row">
                        @if (count($student->trainingNotes) >= 1)
                            <div class="col">
                                <table id="dataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col"><span>Title</span></th>
                                            <th scope="col"><span>Content</span></th>
                                            <th scope="col"><span>Published on</span></th>
                                            <th scope="col"><span>Published By</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($student->trainingNotes as $notes)
                                            <tr @if($notes->author_id === 1) class="text-info" @endif>
                                                <th scope="row" class="font-weight-bold blue-text">{{ $notes->title }}</th>
                                                <td><span>{{ $notes->content }}</span></td>
                                                <td><span>{{ $notes->created_at->diffForHumans() }}</span></td>
                                                <td>{{ $notes->instructor->user->fullName('FLC') }}</td>
                                            </tr>
                                        @endforeach
                                </table>
                            </div>
                        @else
                            <div class="col">
                                <span>No staff comments available!</span>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complete Student Modal -->
        <div class="modal fade" id="completeTrainingModal" tabindex="-1" aria-labelledby="completeTrainingLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeTrainingLabel">Complete Training</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        This action will complete this student's training. Are you sure?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitCompleteTrainingForm()">Yes, Complete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Label Modal -->
        <div class="modal fade" id="assignLabelModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-dialog-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign a New Label</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('training.students.assign.label', $student->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            @foreach ($labels as $label)
                                <div class="mb-2">
                                    <div class="card label-card" data-id="{{ $label->id }}"
                                        style="cursor: pointer; height: 37px; align-items: center; padding: 3px; background: {{ $label->color }}; filter: brightness(0.8);">
                                        <span style="font-size: 20px; margin-right: 5px;">{{ $label->labelHtml() }}</span>
                                    </div>
                                </div>
                            @endforeach
                            <input type="hidden" name="student_label_id" id="selectedLabel" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-light" type="submit">Assign</button>
                        <button type="button" class="btn btn-sm btn-red" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Assign Instructor Modal -->
        <div class="modal fade" id="assignInstructorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Instructor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                        <div class="form-group">
                            <select name="instructor" required class="custom-select">
                                <option value="" selected hidden>Please choose one!</option>
                                @foreach ($instructors as $instructor)
                                    @if ($instructor->id != 1)
                                        <option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Assign</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Instructor Modal -->
        <div class="modal fade" id="confirmRemoveInstructorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Instructor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST" id="instructorForm">
                    @csrf
                    <div class="modal-body">
                    <div class="form-group">
                        <br>
                        <div class="text-center">
                            Change Instructor
                        </div>
                        <select name="instructor" class="custom-select" id="instructorSelect">
                            <option value="">— No Change —</option>
                            @foreach ($instructors as $instructor)
                                @if ($instructor->id != 1)
                                    <option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <div class="text-center">OR</div>
                        <div class="form-group mt-3 text-center">
                            <button type="button" class="btn btn-outline-danger" id="unassignBtn">
                                <i class="fas fa-user-slash"></i> Unassign Current Instructor
                            </button>
                            <input type="hidden" name="remove_instructor" id="removeInstructor" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success" id="confirmBtn" disabled>Confirm</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        window.handlePromotionClick = function(isLastLabel) {
            if (isLastLabel) {
                new bootstrap.Modal(document.getElementById('completeTrainingModal')).show();
            } else {
                document.getElementById('promotionForm').submit();
            }
        };

        window.submitCompleteTrainingForm = function() {
            document.getElementById('promotionForm').submit();
        };
    });

    $(document).ready(function() {
        $('.label-card').click(function() {
            $('.label-card').removeClass('selected').css('border', '1px solid transparent');
            $(this).addClass('selected');
            const labelId = $(this).data('id');
            $('#selectedLabel').val(labelId);
            $(this).css('border', '1px solid #ffffff');
        });
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

    $(document).ready(function () {
        const confirmBtn = $('#confirmBtn');
        const instructorSelect = $('#instructorSelect');
        const removeInstructorInput = $('#removeInstructor');
        const unassignBtn = $('#unassignBtn');

        function updateConfirmButtonState() {
            const hasSelectedInstructor = instructorSelect.val();
            const isUnassign = removeInstructorInput.val() === '1';

            if (hasSelectedInstructor || isUnassign) {
                confirmBtn.prop('disabled', false).removeClass('btn-outline-success').addClass('btn-success');
            } else {
                confirmBtn.prop('disabled', true).removeClass('btn-success').addClass('btn-outline-success');
            }

            if (isUnassign) {
                unassignBtn.removeClass('btn-outline-danger').addClass('btn-danger');
            } else {
                unassignBtn.removeClass('btn-danger').addClass('btn-outline-danger');
            }
        }

        instructorSelect.change(function () {
            removeInstructorInput.val('0');
            unassignBtn.removeClass('active');
            updateConfirmButtonState();
        });

        unassignBtn.click(function () {
            if ($(this).hasClass('btn-danger')) {
                $(this).removeClass('btn-danger').addClass('btn-outline-danger');
                removeInstructorInput.val('0');
            } else {
                $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                instructorSelect.val('');
                removeInstructorInput.val('1');
            }
            updateConfirmButtonState();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const globalForm = document.querySelector('#header-complete-selected').closest('form');
        const globalButton = document.getElementById('header-complete-selected');
        const selectedItems = new Set();

        document.querySelectorAll('.toggle-select-btn').forEach(button => {
            button.addEventListener('click', function () {
                const itemId = this.dataset.itemId;

                if (selectedItems.has(itemId)) {
                    selectedItems.delete(itemId);
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-success');
                } else {
                    selectedItems.add(itemId);
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                }

                globalButton.disabled = selectedItems.size === 0;

            });
        });

        globalForm.addEventListener('submit', function (e) {

            globalForm.querySelectorAll('input[name="checklist_items[]"]').forEach(el => el.remove());

            selectedItems.forEach(itemId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'checklist_items[]';
                input.value = itemId;
                globalForm.appendChild(input);
            });

            if (selectedItems.size === 0) {
                e.preventDefault();
            }
        });
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
                    container.innerHTML = '<span class="alert">No training notes available!</span>';
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

@stop
