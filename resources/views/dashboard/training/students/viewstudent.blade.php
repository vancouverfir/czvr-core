@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
@include('includes.trainingMenu')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <div class="container" style="margin-top: 20px; margin-bottom: 30px;">
        <div class="row">
            <div class="col">  
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row w-100 align-items-center h-100">
                            <img src="{{$student->user->avatar()}}" style="height: 50px; width: 50px; margin-right: 15px; border-radius: 50%;">
                            <div class="d-flex flex-column w-100">
                                <h5 class="list-group-item">
                                    {{$student->user->fullName('FLC')}} - {{$student->user->rating_short}}
                                    <div class="d-flex flex-wrap mt-2">
                                        @foreach($student->labels as $label)
                                            <span class="mr-2 mb-1" style="background-color: {{$label->label->color}};">
                                                <a href="{{route('training.students.drop.label', [$student->id, $label->student_label_id])}}" title="Remove label">
                                                    {{$label->label->labelHtml()}}
                                                </a>
                                            </span>
                                        @endforeach
                                        <a data-toggle="modal" data-target="#assignLabelModal" title="Add label">
                                            <i style="font-size: 0.7em;" class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </h5>
                            </div>
                        </div>
                        <h5 class="mt-3 font-weight-bold">Instructor</h5>
                        @if ($student->instructor)
                            <div class="d-flex flex-column w-50 align-items-left">
                                <h5 class="list-group-item" style="background-color: transparent; border: none;">
                                    <img src="{{$student->instructor->user->avatar()}}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;">
                                    {{$student->instructor->user->fullName('FLC')}}
                                </h5>
                            </div>
                        @else
                            <span style="color: red;">No Instructor Assigned!</span>
                        @endif
                    </div>
                </div>
            <br>
            <div class="card" style="max-height: 700px; overflow-y: auto;">
                <div class="card-body">
                <h3 class="font-weight-bold blue-text">Student Checklist</h3>
                    

                    @if ($studentChecklistGroups->count() > 0)
                    <button id="toggle-completed" class="btn btn-sm btn-outline-info mb-3">Hide Completed</button>
                        @foreach ($studentChecklistGroups as $checklistName => $items)
                            <div class="mb-4 border p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2 font-weight-bold">{{ $checklistName }}</h5>
                                    <form method="POST" action="{{ route('training.students.checklist.deleteChecklist', [$student->id, $checklistName]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this checklist?')">Delete Checklist</button>
                                    </form>
                                </div>
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
                                                <td>
                                                    @if (!$item->completed)
                                                        <form action="{{ route('training.students.checklist.complete', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success">Complete</button>
                                                        </form>
                                                    @else
                                                        <div style="text-align:center">Completed</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @else
                    <br><p>No Checklists Assigned!</p>
                    @endif
                </div>
            </div>
            <br>
          </div>
          <div class="col"> 
                <div class="card">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Info</h3>
                            <h7 class="list-group-item" style="background: transparent"><a href="{{ url('/roster/' . $student->user->id) }}" style="color: inherit;">View Roster Profile </a></h7>
                            <h7 class="list-group-item" style="background: transparent">Joined {{$student->created_at?->format('j F Y') ?? 'N/A'}}</h7>
                            @if ($student->instructor)
                                <h7 class="list-group-item" style="background: transparent; color: #2cb82c; cursor: pointer" data-toggle="modal" data-target="#confirmRemoveInstructorModal">Edit Instructor</h7>
                            @else
                                <h7 class="list-group-item" style="background: transparent; color: #2cb82c; cursor: pointer" data-toggle="modal" data-target="#assignInstructorModal">Assign Instructor</h7>
                            @endif
                            @if (Auth::user()->permissions >= 4)
                            <h7 class="list-group-item" style="background: transparent; color: #ff0000; cursor: pointer"><a href="{{ route('training.students.delete', $student->id) }} " style="color: inherit">Delete Student</a></h7>
                            @endif
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Assign Checklist</h3>
                            <form action="{{ route('training.students.assign.checklist', $student->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <select name="checklist_id" class="custom-select" required>
                                        <option value="" disabled selected>Select Checklist</option>
                                        @foreach ($checklists as $checklist)
                                            <option value="{{ $checklist->id }}">{{ $checklist->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Assign Checklist</button>
                            </form>
                        </div>
                    </div>
                </div>
            <br>
            <div class="card">
                <div class="card-body">
                    <h3 class="font-weight-bold blue-text">Latest Training Notes</h3>
                    <div class="row">
                        <div class="col">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Content</th>
                                        <th scope="col">By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($trainingNotes as $note)
                                        <tr>
                                            <td class="blue-text">{{ $note['training_note'] }}</td>
                                            <td>{{ $note['instructor_name'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No training notes available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br>
          </div>
        </div>
    </div>
    
    <div class="modal fade" id="assignLabelModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign a New Label</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('training.students.assign.label', $student->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        @foreach ($labels as $label)
                            <div class="mb-2">
                                <div class="card label-card" data-id="{{$label->id}}" style="cursor: pointer; height: 37px; align-items: center; padding: 3px; background: {{$label->color}}; filter: brightness(0.8);">
                                    <span style="font-size: 20px; margin-right: 5px;">{{$label->labelHtml()}}</span>
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
                            <option value="" selected hidden>Please choose one..</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>
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
                        <div class="text-center">Change Instructor</div>
                        <select name="instructor" class="custom-select" id="instructorSelect">
                            <option value="">— No Change —</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>
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
</script>
@stop
