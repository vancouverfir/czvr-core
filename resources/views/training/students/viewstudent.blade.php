@extends('layouts.master')
@section('navbarprim') @parent @stop

@push('styles')
<link href="{{ asset('css/pages/training-availability.css') }}" rel="stylesheet">
<style>
.student-tabs { background: var(--czvr-surface-muted); border: 1px solid var(--czvr-border); border-radius: 12px; padding: 6px; }
.student-tabs .nav-link { border-radius: 10px; color: var(--czvr-text-muted); font-weight: 500; }
.student-tabs .nav-link:hover, .student-tabs .nav-link.active { background: var(--czvr-green-soft) !important; color: var(--czvr-green-dark); }
.student-overview, .student-panel { box-shadow: none; }
.student-overview .card-body, .student-panel-body { padding: 0.85rem; }
.student-identity { min-width: 240px; }
.student-name, .student-panel-title { font-size: 0.92rem; font-weight: 500; margin: 0; }
.student-meta, .student-panel-subtitle { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); margin-top: 0.15rem; }
.student-label { align-items: center; border-radius: 0.3rem; color: #fff; display: inline-flex; font-size: var(--czvr-font-size-base); line-height: 1.2; padding: 0.22rem 0.45rem; }
.student-label > span { align-items: center; display: inline-flex; gap: 0.3rem; }
.student-label button { all: unset; align-items: center; cursor: pointer; display: inline-flex; gap: 0.3rem; }
.student-label-add { border: 1px dashed var(--czvr-border); border-radius: 0.3rem; color: var(--czvr-text-muted); height: 1.55rem; width: 1.75rem; }
.student-facts { display: grid; flex: 1; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); }
.student-fact { border-left: 1px solid var(--czvr-border); min-width: 0; padding: 0.3rem 1rem; }
.student-fact-label { color: var(--czvr-text-muted); display: block; font-size: var(--czvr-font-size-base); margin-bottom: 0.3rem; text-transform: uppercase; }
.student-fact-value { align-items: center; display: flex; font-size: var(--czvr-font-size-base); gap: 0.4rem; }
.student-fact-link { cursor: pointer; }
.student-fact-link:hover, .student-action:hover, .student-action:focus-visible { color: var(--czvr-green-dark); }
.student-panel-header { align-items: center; background: var(--czvr-surface); border-bottom: 1px solid var(--czvr-border); display: flex; justify-content: space-between; min-height: 3.25rem; padding: 0.7rem 0.85rem; z-index: 2; }
.student-action { align-items: center; background: transparent; border: 0; border-radius: 0.4rem; color: var(--czvr-text); display: flex; font-size: var(--czvr-font-size-base); gap: 0.65rem; min-height: 2.2rem; padding: 0.45rem 0.55rem; text-align: left; text-decoration: none; width: 100%; }
.student-action:hover, .student-action:focus-visible { background: var(--czvr-green-soft); }
.student-action:disabled { background: transparent; cursor: not-allowed; opacity: 0.55; }
.student-action i { color: var(--czvr-text-muted); width: 1rem; }
.assign-label-grid { display: grid; gap: 0.5rem; }
.assign-label-option { align-items: center; background: var(--label-colour); border-radius: 0.45rem; color: #fff; cursor: pointer; display: flex; font-size: var(--czvr-font-size-base); gap: 0.6rem; justify-content: center; margin: 0; padding: 0.6rem 0.7rem; transition: box-shadow 120ms ease; }
.btn-check:checked + .assign-label-option { box-shadow: 0 0 0 2px var(--czvr-surface), 0 0 0 4px var(--label-colour); }
.btn-check:focus-visible + .assign-label-option { outline: 2px solid var(--czvr-green); outline-offset: 3px; }
.student-action-danger { color: #b42318; }
.student-action-danger:hover, .student-action-danger:focus-visible { background: #fff1f0; color: #8f1c13; }
.student-automation-note, .activity-bubble { background: var(--czvr-surface-muted); border-radius: 0.45rem; }
.student-automation-note { align-items: flex-start; color: var(--czvr-text-muted); display: flex; font-size: var(--czvr-font-size-base); gap: 0.55rem; line-height: 1.45; margin-bottom: 0.45rem; padding: 0.6rem; }
.student-automation-note i { color: var(--czvr-green-dark); margin-top: 0.15rem; }
.student-joined { border-top: 1px solid var(--czvr-border); color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); margin-top: 0.5rem; padding: 0.65rem 0.55rem 0; }
.checklist-scroll { max-height: 700px; overflow-y: auto; }
.checklist-group { border: 1px solid var(--czvr-border); border-radius: 0.55rem; margin-bottom: 0.75rem; overflow: hidden; }
.checklist-group-title { background: var(--czvr-surface-muted); border-bottom: 1px solid var(--czvr-border); font-size: var(--czvr-font-size-base); font-weight: 500; margin: 0; padding: 0.6rem 0.75rem; }
.checklist-group td { border-color: var(--czvr-border); font-size: var(--czvr-font-size-base); padding: 0.48rem 0.75rem; }
.activity-icon { align-items: center; background: var(--czvr-green); border-radius: 50%; display: flex; flex-shrink: 0; height: 36px; justify-content: center; width: 36px; }
.activity-bubble { border-left: 3px solid var(--czvr-green); flex: 1; padding: 12px; }
@media (max-width: 767.98px) {
    .student-overview .card-body { align-items: flex-start !important; flex-direction: column; }
    .student-facts { grid-template-columns: 1fr; width: 100%; }
    .student-fact { border-left: 0; border-top: 1px solid var(--czvr-border); padding: 0.7rem 0; }
}
</style>
@endpush

@section('content')
@include('includes.trainingMenu')

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <a href="{{ route('training.students.students') }}" class="small"><i class="fas fa-arrow-left me-1"></i>Students</a>
        </div>
    </header>
    <ul class="nav nav-pills student-tabs mb-4">
        <li class="nav-item flex-fill"><a class="nav-link active text-center" data-bs-toggle="tab" href="#student-info"><i class="fas fa-address-card me-1"></i>Info</a></li>
        @can('training.instruct')
        <li class="nav-item flex-fill"><a class="nav-link text-center" data-bs-toggle="tab" href="#staff-comments"><i class="fas fa-signature me-1"></i>Comments</a></li>
        <li class="nav-item flex-fill"><a class="nav-link text-center" data-bs-toggle="tab" href="#activity-log"><i class="fas fa-robot me-1"></i>Activity</a></li>
        @endcan
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="student-info">
            @foreach($student->labels->reject(fn ($link) => ! $isVisitor && in_array($link->label->name, \App\Models\AtcTraining\Student::MANAGED_TRAINING_LABELS, true)) as $label)
            <form id="drop-label-{{$label->student_label_id}}" method="POST" action="{{route('training.students.drop.label', [$student->id, $label->student_label_id])}}" class="d-none">
                @csrf
                @method('DELETE')
            </form>
            @endforeach

            <div class="card student-overview mb-3">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="student-identity d-flex align-items-center gap-3">
                        <img src="{{$student->user->avatar()}}" class="rounded-circle" width="52" height="52" alt="">
                        <div>
                            <h2 class="student-name">{{$student->user->fullName('FLC')}}</h2>
                            <div class="student-meta">{{ $student->user->rating_short }} · {{ $student->user->homeFacilityName() }}</div>
                            <div class="d-flex flex-wrap align-items-center gap-1 mt-2">
                                @foreach($student->labels as $label)
                                <span class="student-label" style="background-color: {{$label->label->color}};">
                                    @if (! $isVisitor && in_array($label->label->name, \App\Models\AtcTraining\Student::MANAGED_TRAINING_LABELS, true))
                                        <span title="Managed by the training workflow">
                                            @if ($label->label->fa_icon)<i class="{{ $label->label->fa_icon }} fa-fw" aria-hidden="true"></i>@endif{{ $label->label->name }}
                                        </span>
                                    @else
                                        <button type="submit" form="drop-label-{{$label->student_label_id}}" title="Remove label">
                                            @if ($label->label->fa_icon)<i class="{{ $label->label->fa_icon }} fa-fw" aria-hidden="true"></i>@endif{{ $label->label->name }}
                                        </button>
                                    @endif
                                </span>
                                @endforeach
                                <button type="button" class="student-label-add" data-bs-toggle="modal" data-bs-target="#assignLabelModal" aria-label="Assign an additional label" title="Assign an additional label"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="student-facts">
                        <div class="student-fact student-fact-link" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#confirmRemoveInstructorModal">
                            <span class="student-fact-label">Instructor · VATCAN</span>
                            <span class="student-fact-value">
                                @if ($student->instructor)
                                    <img src="{{ $student->instructor->user->avatar() }}" class="rounded-circle" width="24" height="24" alt=""> {{ $student->instructor->user->fullName('FLC') }}
                                @else
                                    <i class="far fa-user"></i> Not assigned
                                @endif
                            </span>
                        </div>
                        <div class="student-fact student-fact-link" role="link" tabindex="0" onclick="window.location='{{ url('/roster/' . $student->user->id . '/connections') }}'" onkeydown="if (event.key === 'Enter') this.click();">
                            <span class="student-fact-label">Activity</span>
                            <span class="student-fact-value"><i class="far fa-clock"></i>{{ ($student->user->rosterProfile?->currency ?? 0) == 0 ? 'No hours recorded' : decimal_to_hm($student->user->rosterProfile->currency) . ' hours recorded' }}</span>
                        </div>
                        <div class="student-fact student-fact-link" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#editTimesModal">
                            <span class="student-fact-label">Availability</span>
                            <span class="student-fact-value align-items-start">
                                <i class="far fa-calendar mt-1"></i>
                                @if (filled($student->availability))
                                    <span>
                                        @foreach ($student->availabilityPeriods() as $period)
                                            <span class="d-block">{{ $period['days'] }} {{ $period['start'] }}–{{ $period['end'] }} UTC</span>
                                        @endforeach
                                    </span>
                                @else
                                    Not submitted yet
                                @endif
                            </span>
                        </div>
                        @if ($student->consolidationIsPending())
                        <div class="student-fact">
                            <span class="student-fact-label">Consolidation</span>
                            <span class="student-fact-value">
                                <i class="far fa-hourglass-half"></i> {{ number_format($student->consolidationHours(), 1) }} / {{ number_format(\App\Services\TrainingProgressionService::CONSOLIDATION_HOURS, 0) }} hours
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-start">
                <div class="col-lg-7">

                    <div class="card student-panel checklist-scroll mb-3">
                        <div class="student-panel-header sticky-top">
                            <h3 class="student-panel-title blue-text">Student Checklist</h3>
                            @if ($studentChecklistGroups->count() > 0)
                            <div class="d-flex align-items-center gap-2">
                                <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" class="mb-0 d-flex align-items-center gap-2">
                                    @csrf <button type="submit" class="btn btn-primary btn-sm" id="header-apply-selected" disabled>Apply</button>
                                </form>
                                <button id="toggle-completed" class="btn btn-sm btn-outline-secondary">Hide completed</button>
                            </div>
                            @endif
                        </div>
                        <div class="student-panel-body">
                            @if ($studentChecklistGroups->count() > 0)
                            @foreach ($studentChecklistGroups as $checklistName => $items)
                            <div class="checklist-group">
                                <h3 class="checklist-group-title">{{ $checklistName }}</h3>
                                @if ($items->count() > 0 && $items->every(fn($item) => $item->completed))
                                <div class="text-success px-3 py-3"><i class="fas fa-check-circle me-1"></i> Checklist completed</div>
                                @else
                                <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}">@csrf
                                    <table class="table table-sm table-hover mb-0"><tbody>
                                        @foreach ($items as $item)
                                        <tr class="{{ $item->completed ? 'completed-row' : '' }}"><td><span class="toggle-select-item" data-item-id="{{ $item->id }}" data-completed="{{ $item->completed }}" style="cursor:pointer; {{ $item->completed ? 'text-decoration: line-through; color: grey;' : '' }}">{{ $item->checklistItem->item }}</span></td></tr>
                                        @endforeach
                                    </tbody></table>
                                </form>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <span>No checklists assigned!</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card student-panel mb-3">
                        <div class="student-panel-header">
                            <h3 class="student-panel-title blue-text">Actions</h3>
                        </div>
                        <div class="student-panel-body">
                            @php
                            $isWaitlist = $student->isWaitlisted(); $Actions = in_array((int) $student->status, [\App\Models\AtcTraining\Student::STATUS_MARKED_FOR_REMOVAL, \App\Models\AtcTraining\Student::STATUS_COMPLETED], true);
                            $buttonLabel = ($isWaitlist && $isVisitor) ? 'Start Visitor Training' : ($isWaitlist ? 'Start Training' : 'Promote Visitor');
                            @endphp
                            <div class="d-grid gap-1">
                            @if (!$Actions)
                                @if ($student->isConsolidating())
                                <div class="student-action text-muted"><i class="far fa-hourglass-half"></i>Training unlocks after {{ number_format($student->consolidationRemainingHours(), 1) }} more hours</div>
                                @can('training.consolidation.override')
                                <form method="POST" action="{{ route('training.students.consolidation.waive', $student) }}" onsubmit="return confirm('Waive the remaining consolidation requirement for this student?');">@csrf <button type="submit" class="student-action"><i class="fas fa-forward"></i>Waive consolidation</button></form>
                                @endcan
                                @elseif ($student->isRecertification())
                                <button type="button" class="student-action" data-bs-toggle="modal" data-bs-target="#completeTrainingModal"><i class="fas fa-check"></i>Complete Recertification</button>
                                @elseif ($isWaitlist || $isVisitor)
                                @if ($isVisitor || ! $student->startsWithRecertification())
                                <form method="POST" action="{{ $isVisitor ? route('training.students.promoteVisitor', $student->id) : route('training.students.promote', $student->id) }}">@csrf <button type="submit" class="student-action"><i class="fas fa-arrow-up"></i>{{ $buttonLabel }}</button></form>
                                @endif
                                @if ($canStartRecertification && $recertificationPackages->isNotEmpty())
                                <button type="button" class="student-action" data-bs-toggle="modal" data-bs-target="#startRecertificationModal"><i class="fas fa-rotate"></i>Start Recertification</button>
                                @elseif ($canStartRecertification)
                                <button type="button" class="student-action" disabled><i class="fas fa-rotate"></i>Start Recertification</button>
                                <div class="small text-muted">Not every training package up to {{ $student->user->rating_short }} has a checklist mapped, so recertification cannot start!</div>
                                @endif
                                @elseif ($nextLabel)
                                @php
                                    $consolidationHours = number_format(\App\Services\TrainingProgressionService::CONSOLIDATION_HOURS, 0);
                                    $nextStep = match ($student->currentTrainingLabel(\App\Models\AtcTraining\Student::RATING_TRAINING_LABELS)) {
                                        'S1 Training' => 'Complete every S1 checklist item to unlock S2 Training!',
                                        'S2 Training' => "Student must pass the S2 OTS, then log {$consolidationHours} hours of consolidation to unlock S3 Training!",
                                        'S3 Training' => "Student must pass the S3 OTS, then log {$consolidationHours} hours of consolidation to unlock C1 Training!",
                                        'C1 Training' => 'Student must pass the C1 OTS to complete training!',
                                        default => null,
                                    };
                                @endphp
                                @if ($nextStep)
                                <div class="student-automation-note"><i class="fas fa-rotate"></i><span>{{ $nextStep }}</span></div>
                                @endif
                                @else <button type="button" class="student-action" data-bs-toggle="modal" data-bs-target="#completeTrainingModal"><i class="fas fa-graduation-cap"></i>Complete Training</button> @endif
                            <form method="POST" action="{{ route('training.students.assignT2', $student->id) }}">@csrf <button type="submit" class="student-action"><i class="fas fa-list-check"></i>Add Tier 2 Checklists</button></form>
                            @can('roster.manage') <a href="{{ url('/dashboard/roster/edit/' . $student->user->id) }}" class="student-action"><i class="fas fa-certificate"></i>Edit Certifications</a> @endcan
                            @elseif ($student->status === \App\Models\AtcTraining\Student::STATUS_COMPLETED)
                            <div class="student-action text-success"><i class="fas fa-check-circle"></i>Training completed {{ $student->updated_at?->format('F j, Y') }}</div>
                            @endif
                            @can('training.manage') <a href="{{ route('training.students.preview', $student->id) }}" class="student-action"><i class="far fa-eye"></i>Preview as Student</a> @endcan
                            @can('training.students.delete') <button type="button" class="student-action student-action-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal"><i class="far fa-trash-can"></i>Delete Student</button> @endcan
                            </div>
                            <div class="student-joined"><i class="far fa-calendar-plus me-1"></i>Joined {{$student->created_at?->format('F j, Y')}}</div>
                        </div>
                    </div>

                    <div class="card student-panel mb-3">
                        <div class="student-panel-header">
                            <div class="d-flex justify-content-between align-items-center gap-2 w-100">
                                <div>
                                    <h3 class="student-panel-title blue-text">Latest 3 VATCAN Notes</h3>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="https://vatcan.ca/manage/training/notes/controller/{{ $student->user_id }}" target="_blank" rel="noopener" class="btn btn-sm btn-success">New VATCAN Note</a>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#allVatcanNotes">View all</button>
                                </div>
                            </div>
                        </div>
                        <div class="student-panel-body">
                            @if (empty($vatcanNotes)) <p class="text-muted mb-0">No training notes yet! Check back later to view your latest 3!</p>
                            @else
                            @foreach (array_slice($vatcanNotes, 0, 3) as $note)
                                @include('training.students.partials.vatcan-note', ['note' => $note])
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('training.instruct')
        <div class="tab-pane fade" id="staff-comments">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="fw-bold blue-text mb-0"><i class="fas fa-signature me-1"></i> Staff Comments </h3>
                        <a class="btn btn-sm btn-success" href="{{ route('view.add.note', $student->id) }}"><i class="fas fa-plus me-1"></i> New Comment</a>
                    </div>
                    @php
                        $waitlistActivityTitles = ['Waitlist Contacted', 'Waitlist Contact Cleared'];
                        $staffNotes = $student->trainingNotes
                            ->whereNotNull('author_cid')
                            ->whereNotIn('title', $waitlistActivityTitles)
                            ->sortByDesc('created_at');
                    @endphp
                    @forelse ($staffNotes as $notes)
                    <div class="d-flex gap-3 mb-3">
                        <div class="activity-icon"><i class="fas fa-comment text-white"></i></div>
                        <div class="activity-bubble">
                            <div class="d-flex justify-content-between mb-1"><strong class="text-success">{{ $notes->title }}</strong><small>{{ $notes->created_at->diffForHumans() }}</small></div>
                            <div style="color: var(--czvr-text); font-size: 0.9rem; margin-bottom: 8px;">{!! $notes->content !!}</div>
                            <small><u>{{ $notes->author?->fullName('FLC') ?? 'Unknown' }}</u></small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5"><i class="fas fa-signature fa-3x mb-3" style="color: var(--czvr-text-muted);"></i><p style="color: var(--czvr-text-muted);">No staff comments yet!</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="activity-log">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold blue-text mb-0"><i class="fas fa-robot me-1"></i> System Events </h3>
                    </div>
                    @php
                        $systemNotes = $student->trainingNotes
                            ->filter(fn ($note) => $note->author_cid === null || in_array($note->title, $waitlistActivityTitles, true))
                            ->sortByDesc('created_at');
                    @endphp
                    @forelse ($systemNotes as $notes)
                    <div class="d-flex gap-3 mb-3">
                        <div class="activity-icon">
                            <i class="fas {{ str_contains($notes->title, 'Assigned') ? 'fa-user-plus' : (str_contains($notes->title, 'Removed') ? 'fa-user-minus' : (str_contains($notes->title, 'Completed') ? 'fa-graduation-cap' : (str_contains($notes->title, 'Promoted') ? 'fa-arrow-up' : (str_contains($notes->title, 'Created') ? 'fa-plus-circle' : (str_contains($notes->title, 'Recertification') ? 'fa-rotate' : (str_contains($notes->title, 'Contact') ? 'fa-envelope' : 'fa-exclamation')))))) }} text-white"></i>
                        </div>
                        <div class="activity-bubble">
                            <div class="d-flex justify-content-between mb-1"><strong class="text-success">{{ $notes->title }}</strong><small>{{ $notes->created_at->diffForHumans() }}</small></div>
                            <div style="color: var(--czvr-text); font-size: 0.9rem;">{!! $notes->content !!}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5"><i class="fas fa-robot fa-3x mb-3" style="color: var(--czvr-text-muted);"></i><p style="color: var(--czvr-text-muted);">No system logs yet!</p></div>
                    @endforelse
                </div>
            </div>
        </div>
        @endcan
    </div>
</main>

@foreach (['editTimes' => ['title' => 'Edit Availability', 'route' => 'training.students.editTimes'], 'completeTraining' => null, 'assignLabel' => null, 'confirmRemoveInstructor' => null] as $modal => $config)
@if ($modal === 'editTimes')
<div class="modal fade" id="{{$modal}}Modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><form method="POST" action="{{ route($config['route'], $student->id) }}">@csrf <div class="modal-header"><h5 class="modal-title">{{$config['title']}}</h5><button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button></div><div class="modal-body">@include('training.students.partials.availability-fields', ['availabilityEditorId' => 'staffAvailability'])</div><div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div></form></div></div></div>
@elseif ($modal === 'completeTraining')
<div class="modal fade" id="completeTrainingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">🎉 Complete {{ $student->isRecertification() ? 'Recertification' : 'Training' }}! 🎉</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fs-5">Are you sure you want to complete <strong class="blue-text">{{ $student->user->fullName('FLC') }}</strong>'s {{ $student->isRecertification() ? 'recertification' : 'training' }}?</p>
                @if ($student->isRecertification())
                    <p class="small text-muted">Their VATSIM rating will not be changed.</p>
                @endif
                <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWg3Z3I1YnFtdmtsanVvMnd3YjE0ODJ4MGVlNW16anlwa2Nzd2Q5MSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/BfyJIRCINN0NxwEcsE/giphy.gif" class="img-fluid mb-3" style="max-height: 200px;" alt="">
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('training.students.completeTraining', $student->id) }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-success">Yes, Complete {{ $student->isRecertification() ? 'Recertification' : 'Training' }}!</button>
                </form>
            </div>
        </div>
    </div>
</div>
@elseif ($modal === 'assignLabel')
@include('training.students.partials.vatcan-notes-modal')

@can('training.students.delete')
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('training.students.destroy', $student->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStudentTitle">Delete Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete <strong>{{ $student->user->fullName('FLC') }}</strong>?</p>
                    <p class="text-danger mb-0">This permanently deletes the student with their comments, sessions, labels and checklists, and cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<div class="modal fade" id="assignLabelModal" tabindex="-1" aria-labelledby="assignLabelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignLabelTitle">Assign a Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('training.students.assign.label', $student->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($labels->isEmpty())
                        <p class="text-muted mb-0">There are no more labels to assign to this student!</p>
                    @else
                        <div class="assign-label-grid">
                            @foreach ($labels as $label)
                                <input type="radio" class="btn-check" name="student_label_id" id="assign-label-{{ $label->id }}" value="{{ $label->id }}" required>
                                <label class="assign-label-option" for="assign-label-{{ $label->id }}" style="--label-colour: {{ $label->color }}">
                                    <i class="{{ $label->fa_icon ?: 'fas fa-tag' }} fa-fw" aria-hidden="true"></i>
                                    <span>{{ $label->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @if ($labels->isNotEmpty())
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-light" type="submit">Assign</button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@else
<div class="modal fade" id="confirmRemoveInstructorModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">VATCAN Instructor</h5><button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button></div><form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST" id="instructorForm">@csrf <div class="modal-body"><p class="small text-muted">Changes are written to VATCAN and synchronized back to the portal.</p><div class="form-group"><label for="instructorSelect" class="form-label">Assigned instructor</label><select name="instructor" class="form-select" id="instructorSelect" data-searchable><option value="">— No Change —</option>@foreach ($instructors as $instructor)@if ($instructor->id != 1)<option value="{{ $instructor->id }}">{{ $instructor->user->fullName('FLC') }}</option>@endif @endforeach</select></div><div class="form-group mt-3"><button type="button" class="btn btn-outline-danger" id="unassignBtn"><i class="fas fa-user-slash"></i> Unassign current instructor</button><input type="hidden" name="remove_instructor" id="removeInstructor" value="0"></div></div><div class="modal-footer"><button type="submit" class="btn btn-outline-success" id="confirmBtn" disabled>Save in VATCAN</button><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button></div></form></div></div></div>
@endif
@endforeach

@if ($canStartRecertification && $recertificationPackages->isNotEmpty())
<div class="modal fade" id="startRecertificationModal" tabindex="-1" aria-labelledby="startRecertificationTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.students.recertification.start', $student) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="startRecertificationTitle">Start Recertification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Their current VATSIM rating will not change. Recertification will include every required stage through {{ $student->user->rating_short }}:</p>
                    <ul class="small mb-0 ps-3">
                        @foreach ($recertificationPackages as $package)
                            <li>{{ $package->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Recertification</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-completed'); if (toggleButton) { let hidden = false; toggleButton.addEventListener('click', function () { document.querySelectorAll('.completed-row').forEach(row => row.style.display = hidden ? '' : 'none'); hidden = !hidden; toggleButton.textContent = hidden ? 'Show completed' : 'Hide completed'; }); }
    const confirmBtn = document.getElementById('confirmBtn'), instructorSelect = document.getElementById('instructorSelect'), removeInstructorInput = document.getElementById('removeInstructor'), unassignBtn = document.getElementById('unassignBtn');
    function updateConfirmButtonState() { const hasSelectedInstructor = instructorSelect?.value, isUnassign = removeInstructorInput?.value === '1'; if (confirmBtn) { confirmBtn.disabled = !(hasSelectedInstructor || isUnassign); confirmBtn.classList.toggle('btn-success', hasSelectedInstructor || isUnassign); confirmBtn.classList.toggle('btn-outline-success', !(hasSelectedInstructor || isUnassign)); } if (unassignBtn) { unassignBtn.classList.toggle('btn-danger', isUnassign); unassignBtn.classList.toggle('btn-outline-danger', !isUnassign); } }
    instructorSelect?.addEventListener('change', function () { removeInstructorInput.value = '0'; unassignBtn?.classList.remove('active'); updateConfirmButtonState(); }); unassignBtn?.addEventListener('click', function () { const isActive = removeInstructorInput.value === '1'; removeInstructorInput.value = isActive ? '0' : '1'; instructorSelect.value = ''; updateConfirmButtonState(); }); updateConfirmButtonState();
    const globalButton = document.getElementById('header-apply-selected'); if (globalButton) { const globalForm = globalButton.closest('form'), selectedItems = new Set(); document.querySelectorAll('.toggle-select-item').forEach(item => { const isCompleted = item.dataset.completed === '1'; if (isCompleted) { item.style.textDecoration = 'line-through'; item.style.color = 'grey'; } item.addEventListener('click', function () { const itemId = this.dataset.itemId; if (selectedItems.has(itemId)) { selectedItems.delete(itemId); if (isCompleted) { this.style.textDecoration = 'line-through'; this.style.color = 'grey'; } else { this.style.textDecoration = 'none'; this.style.color = ''; } } else { selectedItems.add(itemId); if (isCompleted) { this.style.textDecoration = 'none'; this.style.color = ''; } else { this.style.textDecoration = 'line-through'; this.style.color = 'grey'; } } globalButton.disabled = selectedItems.size === 0; }); }); globalForm?.addEventListener('submit', function (e) { globalForm.querySelectorAll('input[name="checklist_items[]"]').forEach(el => el.remove()); selectedItems.forEach(itemId => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'checklist_items[]'; input.value = itemId; globalForm.appendChild(input); }); if (selectedItems.size === 0) e.preventDefault(); }); }
});
</script>
<script src="{{ asset('js/training-availability.js') }}"></script>
@stop
