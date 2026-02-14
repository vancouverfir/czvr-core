@extends('layouts.master')
@section('navbarprim') @parent @stop

<style>
.instructor:hover, .editable:hover { color: #2cb82c; }
.student-tabs .nav-link { color: #aaa; font-weight: 500; border-radius: 10px; transition: all 0.2s; }
.student-tabs .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
.student-tabs .nav-link.active { color: #fff; background: rgba(255,255,255,0.15) !important; }
.activity-message { display: flex; gap: 12px; margin-bottom: 15px; }
.activity-icon { width: 36px; height: 36px; background: #2cb82c; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.activity-bubble { flex: 1; background: rgba(255,255,255,0.05); border-radius: 8px; padding: 12px; border-left: 3px solid #2cb82c; }
</style>

@section('content')
@include('includes.trainingMenu')

<div class="container" style="margin-top: 20px; margin-bottom: 30px;">
    <ul class="nav nav-pills student-tabs mb-4" style="background: rgba(0,0,0,0.3); border-radius: 12px; padding: 6px;">
        <li class="nav-item flex-fill"><a class="nav-link active text-center" data-toggle="tab" href="#student-info"><i class="fas fa-address-card mr-1"></i>Info</a></li>
        @if (auth()->user()->permissions >= 3)
        <li class="nav-item flex-fill"><a class="nav-link text-center" data-toggle="tab" href="#staff-comments"><i class="fas fa-signature mr-1"></i>Comments <span class="badge badge-light ml-1" style="background: rgba(255,255,255,0.1); color: #ccc; font-size: 0.7rem;"></span></a></li>
        <li class="nav-item flex-fill"><a class="nav-link text-center" data-toggle="tab" href="#activity-log"><i class="fas fa-robot mr-1"></i>Activity <span class="badge badge-light ml-1" style="background: rgba(255,255,255,0.1); color: #ccc; font-size: 0.7rem;"></span></a></li>
        @endif
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="student-info">
            <div class="row">
                <div class="col">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="{{$student->user->avatar()}}" style="height: 50px; width: 50px; margin-right: 15px; border-radius: 50%;">
                                <div class="flex-grow-1">
                                    <h5 class="list-group-item">{{$student->user->fullName('FLC')}} - {{$student->user->rating_short}} {{$student->user->division_code}} {{$student->user->region_code}} {{$student->user->subdivision_code}}
                                        <div class="d-flex flex-wrap mt-2">
                                            @foreach($student->labels as $label)
                                            <span class="mr-2 mb-1" style="background-color: {{$label->label->color}};"><a href="{{route('training.students.drop.label', [$student->id, $label->student_label_id])}}">{{$label->label->labelHtml()}}</a></span>
                                            @endforeach
                                            <a data-toggle="modal" data-target="#assignLabelModal"><i class="fas fa-plus" style="font-size: 0.7em; position: relative; top: 3px;"></i></a>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <h5 class="mt-3 font-weight-bold instructor" style="cursor: pointer;" data-toggle="modal" data-target="#confirmRemoveInstructorModal">Instructor</h5>
                            @if ($student->instructor)
                            <h5 class="list-group-item" style="background: transparent; border: none;"><img src="{{ $student->instructor->user->avatar() }}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 50%;"> {{ $student->instructor->user->fullName('FLC') }}</h5>
                            @else <span>Instructor not assigned</span> @endif
                            <div class="d-flex mt-3" style="gap: 1rem;">
                                <div class="flex-fill">
                                    <h6 class="editable mb-1 font-weight-bold" style="font-size: 0.8rem; cursor: pointer;" onclick="window.location='{{ url('/roster/' . $student->user->id . '/connections') }}'">Activity</h6>
                                    <span style="font-size: 0.85rem;">{{ ($student->user->rosterProfile?->currency ?? 0) == 0 ? 'No hours recorded' : decimal_to_hm($student->user->rosterProfile->currency) . ' hours recorded' }}</span>
                                </div>
                                <div class="flex-fill">
                                    <h6 class="editable mb-1 font-weight-bold" style="font-size: 0.8rem; cursor: pointer;" data-toggle="modal" data-target="#editTimesModal">Availability</h6>
                                    <span>{{ $student->times ?? 'Not submitted yet' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3" style="max-height: 700px; overflow-y: auto;">
                        <div class="card-body">
                            @if ($studentChecklistGroups->count() > 0)
                            <div class="d-flex justify-content-between align-items-center mb-3 sticky-top p-2" style="background-color: #2e2f2f;">
                                <h3 class="font-weight-bold blue-text mb-0">Student Checklist</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" class="mb-0 d-flex align-items-center gap-2">
                                        @csrf <button type="submit" class="btn btn-primary btn-sm" id="header-apply-selected" disabled>Apply</button>
                                    </form>
                                    <button id="toggle-completed" class="btn btn-sm btn-outline-info">Hide Completed</button>
                                </div>
                            </div>
                            @foreach ($studentChecklistGroups as $checklistName => $items)
                            <div class="mb-3 border p-3 rounded">
                                <h3 class="font-weight-bold text-center mb-3">{{ $checklistName }}</h3>
                                @if ($items->count() > 0 && $items->every(fn($item) => $item->completed))
                                <div class="text-center py-3"><h5 class="text-success font-weight-bold">✅ {{ $checklistName }} checklist completed!</h5></div>
                                @else
                                <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}">@csrf
                                    <table class="table table-sm table-hover"><tbody>
                                        @foreach ($items as $item)
                                        <tr class="{{ $item->completed ? 'completed-row' : '' }}"><td><span class="toggle-select-item" data-item-id="{{ $item->id }}" data-completed="{{ $item->completed }}" style="cursor:pointer; {{ $item->completed ? 'text-decoration: line-through; color: grey;' : '' }}">{{ $item->checklistItem->item }}</span></td></tr>
                                        @endforeach
                                    </tbody></table>
                                </form>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <h3 class="font-weight-bold blue-text mb-3">Student Checklist</h3>
                            <span>No checklists assigned!</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Actions</h3>
                            @php
                            $isWaitlist = in_array($student->status, [0, 3]); $isVisitor = in_array($student->status, [3, 5]); $Actions = in_array($student->status, [4, 9]);
                            $buttonLabel = ($isWaitlist && $isVisitor) ? 'Start Visitor Training' : ($isWaitlist ? 'Start Training' : ($isVisitor ? 'Promote Visitor' : 'Promote Student'));
                            @endphp
                            @if (!$Actions)
                            <h7 class="list-group-item" style="background: transparent; color: #2cb82c;">
                                @if ($nextLabel)
                                <form method="POST" action="{{ $isVisitor ? route('training.students.promoteVisitor', $student->id) : route('training.students.promote', $student->id) }}" style="display: inline;">@csrf <button type="submit" style="all: unset; color: inherit; cursor: pointer;">{{ $buttonLabel }}</button></form>
                                @else <button type="button" style="all: unset; color: inherit; cursor: pointer;" data-toggle="modal" data-target="#completeTrainingModal">Complete Training</button> @endif
                            </h7>
                            <h7 class="list-group-item" style="background: transparent;"><form method="POST" action="{{ route('training.students.assignT2', $student->id) }}" style="display: inline;">@csrf <button style="all: unset; color: inherit; cursor: pointer;">Add Tier 2 Checklists</button></form></h7>
                            @if (auth()->user()->permissions >= 3) <h7 class="list-group-item" style="background: transparent"><a href="{{ url('/dashboard/roster/edit/' . $student->user->id) }}" style="color: inherit;">Edit Certifications</a></h7> @endif
                            @elseif ($student->status === 9)
                            <h7 class="list-group-item" style="background: transparent; color: #2cb82c;">Training Completed On {{ $student->updated_at?->format('F j, Y') }}</h7>
                            @endif
                            <h7 class="list-group-item" style="background: transparent">Created {{$student->created_at?->format('F jS Y')}}</h7>
                            @if (Auth::user()->permissions >= 4) <h7 class="list-group-item" style="background: transparent; color: #ff0000; cursor: pointer"><a href="{{ route('training.students.delete', $student->id) }}" style="color: inherit">Delete Student</a></h7> @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="font-weight-bold blue-text mb-0">Latest 3 Vatcan Notes</h3>
                                <div>
                                    <a href="https://vatcan.ca/manage/training/notes/controller/{{ $student->user_id }}" target="_blank" class="btn btn-sm btn-success">New VATCAN Note</a>
                                    <a href="{{ route('training.students.allnotes', $student->id) }}" class="btn btn-sm btn-outline-info">View All</a>
                                </div>
                            </div>
                            <div style="color: #999; font-size: 0.85rem;" class="mt-1">Notes left by non Vancouver FIR members not shown!</div>
                        </div>
                        <div class="card-body">
                            @if (empty($vatcanNotes)) <span class="alert">No training notes available!</span>
                            @else
                            @foreach (array_slice($vatcanNotes, 0, 3) as $note)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-sticky-note mr-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $note['instructor_name'] ?? 'Unknown' }} - {{ ['Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR', 'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'][$note['facility_id']] ?? 'Unknown' }} {{ \Carbon\Carbon::parse($note['friendly_time'])->format('F j, Y') }}</h6>
                                        <div class="mt-1">
                                            <span class="badge bg-info">{{ $note['position_trained'] ?? '' }}</span>
                                            <span class="badge {{ ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'][$note['session_type']] ?? 'bg-secondary' }}">{{ ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'][$note['session_type']] ?? 'Generic' }}</span>
                                        </div>
                                        <p class="mt-3 mb-0" style="white-space: pre-wrap;">{{ $note['training_note'] }}</p>
                                        @if (!empty($note['marking_sheet'])) <hr class="my-3"><a href="{{ $note['marking_sheet'] }}" target="_blank" class="btn btn-outline-info btn-sm"><i class="far fa-list-alt me-1"></i> View Marking Sheet</a> @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->permissions >= 3)
        <div class="tab-pane fade" id="staff-comments">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="font-weight-bold blue-text mb-0"><i class="fas fa-signature mr-1"></i> Staff Comments </h3>
                        <a class="btn btn-sm btn-success" href="{{ route('view.add.note', $student->id) }}"><i class="fas fa-plus mr-1"></i> New Comment</a>
                    </div>
                    @php $staffNotes = $student->trainingNotes->where('author_id', '!=', 1)->sortByDesc('created_at'); @endphp
                    @forelse ($staffNotes as $notes)
                    <div class="activity-message">
                        <div class="activity-icon"><i class="fas fa-comment text-white"></i></div>
                        <div class="activity-bubble">
                            <div class="d-flex justify-content-between mb-1"><strong class="text-success">{{ $notes->title }}</strong><small>{{ $notes->created_at->diffForHumans() }}</small></div>
                            <div style="color: #ccc; font-size: 0.9rem; margin-bottom: 8px;">{!! $notes->content !!}</div>
                            <small><u>{{ $notes->instructor->user->fullName('FLC') }}</u></small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5"><i class="fas fa-signature fa-3x mb-3" style="color: #666;"></i><p style="color: #999;">No staff comments yet!</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="activity-log">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="font-weight-bold blue-text mb-0"><i class="fas fa-robot mr-1"></i> System Events </h3>
                    </div>
                    @php $systemNotes = $student->trainingNotes->where('author_id', 1)->sortByDesc('created_at'); @endphp
                    @forelse ($systemNotes as $notes)
                    <div class="activity-message">
                        <div class="activity-icon">
                            <i class="fas {{ str_contains($notes->title, 'Assigned') ? 'fa-user-plus' : (str_contains($notes->title, 'Removed') ? 'fa-user-minus' : (str_contains($notes->title, 'Completed') ? 'fa-graduation-cap' : (str_contains($notes->title, 'Promoted') ? 'fa-arrow-up' : (str_contains($notes->title, 'Created') ? 'fa-plus-circle' : 'fa-circle')))) }} text-white"></i>
                        </div>
                        <div class="activity-bubble">
                            <div class="d-flex justify-content-between mb-1"><strong class="text-success">{{ $notes->title }}</strong><small>{{ $notes->created_at->diffForHumans() }}</small></div>
                            <div style="color: #ccc; font-size: 0.9rem;">{!! $notes->content !!}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5"><i class="fas fa-history fa-3x mb-3" style="color: #666;"></i><p style="color: #999;">No activity yet!</p></div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@foreach (['editTimes' => ['title' => 'Edit Availability', 'field' => 'times', 'rows' => 3, 'route' => 'training.students.editTimes'], 'completeTraining' => null, 'assignLabel' => null, 'confirmRemoveInstructor' => null] as $modal => $config)
@if ($modal === 'editTimes')
<div class="modal fade" id="{{$modal}}Modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered" style="max-width:395px;"><div class="modal-content"><form method="POST" action="{{ route($config['route'], $student->id) }}">@csrf <div class="modal-header"><h5 class="modal-title">{{$config['title']}}</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body"><div class="form-group"><label>Times</label><textarea name="{{$config['field']}}" rows="{{$config['rows']}}" class="form-control" placeholder="Times in Z!">{{ $student->times }}</textarea></div></div><div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button></div></form></div></div></div>
@elseif ($modal === 'completeTraining')
<div class="modal fade" id="completeTrainingModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg"><div class="modal-header"><h5 class="modal-title">🎉 Complete Training! 🎉</h5><button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button></div><div class="modal-body text-center"><p class="fs-5">Are you sure you want to complete <strong class='blue-text'>{{ $student->user->fullName('FLC') }}</strong>'s training?</p><img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWg3Z3I1YnFtdmtsanVvMnd3YjE0ODJ4MGVlNW16anlwa2Nzd2Q5MSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/BfyJIRCINN0NxwEcsE/giphy.gif" class="img-fluid mb-3" style="max-height: 200px;"></div><div class="modal-footer d-flex justify-content-center"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><form method="POST" action="{{ route('training.students.completeTraining', $student->id) }}" class="m-0">@csrf <button type="submit" class="btn btn-success">Yes, Complete Training!</button></form></div></div></div></div>
@elseif ($modal === 'assignLabel')
<div class="modal fade" id="assignLabelModal"><div class="modal-dialog modal-dialog-centered modal-dialog-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Assign a New Label</h5><button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button></div><form action="{{ route('training.students.assign.label', $student->id) }}" method="POST">@csrf <div class="modal-body">@foreach ($labels as $label)<div class="mb-2"><div class="card label-card" data-id="{{ $label->id }}" style="cursor: pointer; height: 37px; align-items: center; padding: 3px; background: {{ $label->color }}; filter: brightness(0.8);"><span style="font-size: 20px;">{{ $label->labelHtml() }}</span></div></div>@endforeach <input type="hidden" name="student_label_id" id="selectedLabel"></div><div class="modal-footer"><button class="btn btn-sm btn-light" type="submit">Assign</button><button type="button" class="btn btn-sm btn-red" data-dismiss="modal">Cancel</button></div></form></div></div></div>
@else
<div class="modal fade" id="confirmRemoveInstructorModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Edit Instructor</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST" id="instructorForm">@csrf <div class="modal-body"><div class="form-group"><br><div class="text-center">Change Instructor</div><select name="instructor" class="custom-select" id="instructorSelect"><option value="">— No Change —</option>@foreach ($instructors as $instructor)@if ($instructor->id != 1)<option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>@endif @endforeach</select></div><hr><div class="text-center">OR</div><div class="form-group mt-3 text-center"><button type="button" class="btn btn-outline-danger" id="unassignBtn"><i class="fas fa-user-slash"></i> Unassign Current Instructor</button><input type="hidden" name="remove_instructor" id="removeInstructor" value="0"></div></div><div class="modal-footer"><button type="submit" class="btn btn-outline-success" id="confirmBtn" disabled>Confirm</button><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></div></form></div></div></div>
@endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.label-card').forEach(card => card.addEventListener('click', function () { document.querySelectorAll('.label-card').forEach(c => { c.classList.remove('selected'); c.style.border = '1px solid transparent'; }); this.classList.add('selected'); this.style.border = '1px solid #ffffff'; document.getElementById('selectedLabel').value = this.dataset.id; }));
    const toggleButton = document.getElementById('toggle-completed'); if (toggleButton) { let hidden = false; toggleButton.addEventListener('click', function () { document.querySelectorAll('.completed-row').forEach(row => row.style.display = hidden ? '' : 'none'); hidden = !hidden; toggleButton.textContent = hidden ? 'Show Completed' : 'Hide Completed'; }); }
    const confirmBtn = document.getElementById('confirmBtn'), instructorSelect = document.getElementById('instructorSelect'), removeInstructorInput = document.getElementById('removeInstructor'), unassignBtn = document.getElementById('unassignBtn');
    function updateConfirmButtonState() { const hasSelectedInstructor = instructorSelect?.value, isUnassign = removeInstructorInput?.value === '1'; if (confirmBtn) { confirmBtn.disabled = !(hasSelectedInstructor || isUnassign); confirmBtn.classList.toggle('btn-success', hasSelectedInstructor || isUnassign); confirmBtn.classList.toggle('btn-outline-success', !(hasSelectedInstructor || isUnassign)); } if (unassignBtn) { unassignBtn.classList.toggle('btn-danger', isUnassign); unassignBtn.classList.toggle('btn-outline-danger', !isUnassign); } }
    instructorSelect?.addEventListener('change', function () { removeInstructorInput.value = '0'; unassignBtn?.classList.remove('active'); updateConfirmButtonState(); }); unassignBtn?.addEventListener('click', function () { const isActive = removeInstructorInput.value === '1'; removeInstructorInput.value = isActive ? '0' : '1'; instructorSelect.value = ''; updateConfirmButtonState(); }); updateConfirmButtonState();
    const globalButton = document.getElementById('header-apply-selected'); if (globalButton) { const globalForm = globalButton.closest('form'), selectedItems = new Set(); document.querySelectorAll('.toggle-select-item').forEach(item => { const isCompleted = item.dataset.completed === '1'; if (isCompleted) { item.style.textDecoration = 'line-through'; item.style.color = 'grey'; } item.addEventListener('click', function () { const itemId = this.dataset.itemId; if (selectedItems.has(itemId)) { selectedItems.delete(itemId); if (isCompleted) { this.style.textDecoration = 'line-through'; this.style.color = 'grey'; } else { this.style.textDecoration = 'none'; this.style.color = ''; } } else { selectedItems.add(itemId); if (isCompleted) { this.style.textDecoration = 'none'; this.style.color = ''; } else { this.style.textDecoration = 'line-through'; this.style.color = 'grey'; } } globalButton.disabled = selectedItems.size === 0; }); }); globalForm?.addEventListener('submit', function (e) { globalForm.querySelectorAll('input[name="checklist_items[]"]').forEach(el => el.remove()); selectedItems.forEach(itemId => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'checklist_items[]'; input.value = itemId; globalForm.appendChild(input); }); if (selectedItems.size === 0) e.preventDefault(); }); }
});
</script>
@stop
