@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

<style>
    .instructor:hover {
    color: #2cb82c;
    }

    .editable:hover {
        color: #2cb82c;
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
                    @if ($student->instructor)
                        <h5 class="mt-3 font-weight-bold instructor" style="cursor: pointer;" data-toggle="modal" data-target="#confirmRemoveInstructorModal">
                            Instructor
                        </h5>
                        <h5 class="list-group-item" style="background-color: transparent; border: none;">
                            <img src="{{ $student->instructor->user->avatar() }}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;">
                            {{ $student->instructor->user->fullName('FLC') }}
                        </h5>
                    @else
                        <h5 class="mt-3 font-weight-bold instructor" style="cursor: pointer;" data-toggle="modal" data-target="#confirmRemoveInstructorModal">Instructor</h5>
                        <span>Instructor not assigned</span>
                    @endif

                    <div class="d-flex mt-3" style="gap: 1rem;">
                        <div class="flex-fill">
                            <h6 class="mb-1 font-weight-bold" style="font-size: 0.8rem;">Activity</h6>
                            <span style="font-size: 0.85rem;">
                                @if (($student->user->rosterProfile?->currency ?? 0) == 0)
                                    No hours recorded
                                @else
                                    {{ decimal_to_hm($student->user->rosterProfile->currency) }} hours recorded
                                @endif
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="editable mb-1 font-weight-bold" style="font-size: 0.8rem; cursor: pointer;"  data-toggle="modal" data-target="#editTimesModal">Availability</h6>
                            <span>
                                {{ $student->times ?? 'Not submitted yet' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Checklist -->
            <div class="card mb-3" style="max-height: 700px; overflow-y: auto;">
                <div class="card-body">
                    @if ($studentChecklistGroups->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3 sticky-top p-2" style="background-color: #2e2f2f;">
                            <h3 class="font-weight-bold blue-text mb-0">Student Checklist</h3>
                            <div class="d-flex align-items-center gap-2">
                                <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" class="mb-0 d-flex align-items-center gap-2">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm" id="header-apply-selected" disabled>
                                        Apply
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

                                @php
                                    // Check if this checklist group is fully completed
                                    $allCompleted = $items->count() > 0 && $items->every(fn($item) => $item->completed);
                                @endphp

                                @if ($allCompleted)
                                    <div class="text-center py-3">
                                        <h5 class="text-success font-weight-bold">
                                            ✅ {{ $checklistName }} checklist completed in its entirety!
                                        </h5>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" id="checklist-form-{{ Str::slug($checklistName) }}">
                                        @csrf
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $item)
                                                    <tr class="{{ $item->completed ? 'completed-row' : '' }}">
                                                        <td>
                                                            <span class="toggle-select-item"
                                                                  data-item-id="{{ $item->id }}"
                                                                  data-completed="{{ $item->completed }}"
                                                                  style="cursor:pointer; {{ $item->completed ? 'text-decoration: line-through; color: grey;' : '' }}">
                                                                {{ $item->checklistItem->item }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </form>
                                @endif
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
                            $isWaitlist = in_array($student->status, [0, 3]);
                            $isVisitor = in_array($student->status, [3, 5]);

                            $routeName = $isVisitor ? 'training.students.promoteVisitor' : 'training.students.promote';

                            if ($isWaitlist && $isVisitor) {
                                $buttonLabel = 'Start Visitor Training';
                            } elseif ($isWaitlist) {
                                $buttonLabel = 'Start Training';
                            } else {
                                $buttonLabel = $isVisitor ? 'Promote Visitor' : 'Promote Student';
                            }
                        @endphp

                        <form method="POST" action="{{ route($routeName, $student->id) }}" style="display: inline;">
                            @csrf
                            <h7 class="list-group-item" style="background: transparent; color: #2cb82c;">
                                @if ($nextLabel)
                                    <!-- Promote -->
                                    <form method="POST" action="{{ $isVisitor ? route('training.students.promoteVisitor', $student->id) : route('training.students.promote', $student->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="all: unset; color: inherit; cursor: pointer;">
                                            {{ $buttonLabel }}
                                        </button>
                                    </form>
                                @else
                                    <!-- Complete Training -->
                                    <button type="button" style="all: unset; color: inherit; cursor: pointer;" data-toggle="modal" data-target="#completeTrainingModal">
                                        Complete Training
                                    </button>
                                @endif
                            </h7>
                        </form>
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
                        <h3 class="font-weight-bold blue-text mb-0">Latest 3 Vatcan Notes</h3>
                        <div>
                            <a href="https://vatcan.ca/manage/training/notes/controller/{{ $student->user_id }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                New VATCAN Note
                            </a>
                            <a href="{{ route('training.students.allnotes', $student->id) }}" class="text-primary btn btn-sm btn-outline-info ms-2">View All</a>
                        </div>
                    </div>
                    <div class="text-muted small mt-1">
                        Notes left by non Vancouver FIR members not shown!
                    </div>
                </div>

                <div class="card-body">
                    @if (empty($vatcanNotes))
                        <span class="alert">No training notes available! Or you refreshed too many times...</span>
                    @else
                        @php
                            $sessionTypes = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];
                            $sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
                            $afacility = [
                                'Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR',
                                'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'
                            ];
                        @endphp

                        @foreach (array_slice($vatcanNotes, 0, 3) as $note)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="me-3"><i class="far fa-sticky-note mr-1"></i></div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $note['instructor_name'] ?? 'Unknown' }} -
                                            {{ $afacility[$note['facility_id']] ?? 'Unknown' }}
                                            {{ \Carbon\Carbon::parse($note['friendly_time'])->format('F j, Y') }}
                                        </h6>
                                        <div class="mt-1">
                                            <span class="badge bg-info">{{ $note['position_trained'] ?? '' }}</span>
                                            <span class="badge {{ $sessionBadgeColors[$note['session_type']] ?? 'bg-secondary' }}">
                                                {{ $sessionTypes[$note['session_type']] ?? 'Generic' }}
                                            </span>
                                        </div>
                                        <p class="mt-3 mb-0" style="white-space: pre-wrap;">{{ $note['training_note'] }}</p>
                                        @if (!empty($note['marking_sheet']))
                                            <hr class="my-3">
                                            <a href="{{ $note['marking_sheet'] }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="far fa-list-alt me-1"></i> View Marking Sheet
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Comments -->
     @if (auth()->user()->permissions >= 3)
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
        @endif
        </div>

        <!-- Edit Times Modal -->
        <div class="modal fade" id="editTimesModal" tabindex="-1" aria-labelledby="editTimesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:395px;" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('training.students.editTimes', $student->id) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTimesModalLabel">Edit Availability</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="timesInputModal">Times</label>
                                    <textarea name="times" id="timesInputModal" rows="3" class="form-control" placeholder="Times in Z!">{{ $student->times }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Complete Student Modal -->
        <div class="modal fade" id="completeTrainingModal" tabindex="-1" aria-labelledby="completeTrainingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeTrainingModalLabel">
                            🎉 Complete Training! 🎉
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" style="color: white;" aria-label="Close"><span aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body text-center">
                        <p class="fs-5">Are you sure you want to complete <strong class='blue-text'>{{ $student->user->fullName('FLC') }}</strong>'s training?</p>
                        <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWg3Z3I1YnFtdmtsanVvMnd3YjE0ODJ4MGVlNW16anlwa2Nzd2Q5MSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/BfyJIRCINN0NxwEcsE/giphy.gif" alt="Celebration GIF" class="img-fluid mb-3" style="max-height: 200px;">
                    </div>

                    <div class="modal-footer d-flex justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> Cancel </button>

                        <form method="POST" action="{{ route('training.students.completeTraining', $student->id) }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success"> Yes, Complete Training! </button>
                        </form>
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
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.label-card').forEach(card => {
            card.addEventListener('click', function () {
                document.querySelectorAll('.label-card').forEach(c => {
                    c.classList.remove('selected');
                    c.style.border = '1px solid transparent';
                });
                this.classList.add('selected');
                this.style.border = '1px solid #ffffff';
                document.getElementById('selectedLabel').value = this.dataset.id;
            });
        });

        const toggleButton = document.getElementById('toggle-completed');
        if (toggleButton) {
            let hidden = false;
            toggleButton.addEventListener('click', function () {
                document.querySelectorAll('.completed-row').forEach(row => {
                    row.style.display = hidden ? '' : 'none';
                });
                hidden = !hidden;
                toggleButton.textContent = hidden ? 'Show Completed' : 'Hide Completed';
            });
        }

        const confirmBtn = document.getElementById('confirmBtn');
        const instructorSelect = document.getElementById('instructorSelect');
        const removeInstructorInput = document.getElementById('removeInstructor');
        const unassignBtn = document.getElementById('unassignBtn');

        function updateConfirmButtonState() {
            const hasSelectedInstructor = instructorSelect?.value;
            const isUnassign = removeInstructorInput?.value === '1';

            if (confirmBtn) {
                confirmBtn.disabled = !(hasSelectedInstructor || isUnassign);
                confirmBtn.classList.toggle('btn-success', hasSelectedInstructor || isUnassign);
                confirmBtn.classList.toggle('btn-outline-success', !(hasSelectedInstructor || isUnassign));
            }

            if (unassignBtn) {
                unassignBtn.classList.toggle('btn-danger', isUnassign);
                unassignBtn.classList.toggle('btn-outline-danger', !isUnassign);
            }
        }

        instructorSelect?.addEventListener('change', function () {
            removeInstructorInput.value = '0';
            unassignBtn?.classList.remove('active');
            updateConfirmButtonState();
        });

        unassignBtn?.addEventListener('click', function () {
            const isActive = removeInstructorInput.value === '1';
            removeInstructorInput.value = isActive ? '0' : '1';
            instructorSelect.value = '';
            updateConfirmButtonState();
        });

        updateConfirmButtonState();

        const globalButton = document.getElementById('header-apply-selected');
        if (globalButton) {
            const globalForm = globalButton.closest('form');
            const selectedItems = new Set();

            document.querySelectorAll('.toggle-select-item').forEach(item => {
                const isCompleted = item.dataset.completed === '1';

                if (isCompleted) {
                    item.style.textDecoration = 'line-through';
                    item.style.color = 'grey';
                }

                item.addEventListener('click', function () {
                    const itemId = this.dataset.itemId;

                    if (selectedItems.has(itemId)) {
                        selectedItems.delete(itemId);
                        if (isCompleted) {
                            this.style.textDecoration = 'line-through';
                            this.style.color = 'grey';
                        } else {
                            this.style.textDecoration = 'none';
                            this.style.color = '';
                        }
                    } else {
                        selectedItems.add(itemId);
                        if (isCompleted) {
                            this.style.textDecoration = 'none';
                            this.style.color = '';
                        } else {
                            this.style.textDecoration = 'line-through';
                            this.style.color = 'grey';
                        }
                    }

                    globalButton.disabled = selectedItems.size === 0;
                });
            });

            globalForm?.addEventListener('submit', function (e) {
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
        }
    });
    </script>

@stop
