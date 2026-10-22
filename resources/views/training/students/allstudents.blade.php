@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<style>
    .rounded-card {
        border-radius: 0.7rem !important;
        overflow: hidden;
    }
    .swirly-background {
        background: transparent;
        color: var(--czvr-text);
    }
    .list-group-item {
        border: 0;
        border-radius: 0.45rem !important;
        margin: 2px 0;
        padding: 0.65rem 0.7rem;
    }
    .select2-container--default .select2-selection--single {
        background-color: var(--czvr-white) !important;
        color: var(--czvr-text) !important;
        border: 1px solid var(--czvr-border) !important;
    }
    .select2-dropdown {
        background-color: var(--czvr-white) !important;
        color: var(--czvr-text) !important;
    }
    .select2-search__field {
        background-color: var(--czvr-white) !important;
        color: var(--czvr-text) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--czvr-text) !important;
    }
    .select2-container--default .select2-selection__clear {
        color: red !important;
        font-size: 16px;
    }
    #studentSearch::placeholder {
        color: var(--czvr-text-muted);
    }
    .student-list-card {
        background: var(--czvr-surface);
        border: 1px solid var(--czvr-border) !important;
        min-height: 160px;
    }
    .student-list-card h2 {
        font-size: var(--czvr-font-size-base);
        font-weight: 600;
    }
    .student-list-name {
        font-size: var(--czvr-font-size-base);
        font-weight: 500;
    }
    .student-list-number {
        background: var(--czvr-green-soft) !important;
        color: var(--czvr-green-dark) !important;
    }
</style>

<main class="container training-page swirly-background">
    <div class="content">
        <header class="training-page-header">
            <div>
                <h1 class="blue-text">Students</h1>
                <p>VATCAN-synced training stages, consolidation, and assigned instructors!</p>
            </div>
            <button type="button" id="toggleLabels" class="btn btn-sm btn-outline-primary">Expand labels</button>
        </header>

        <div class="form-group mb-2">
            <input type="text" class="form-control" id="studentSearch" placeholder="Search Students...">
        </div>
        <div class="mb-4">
            <a href="{{ route('training.students.completed') }}" class="blue-text" style="text-decoration: none; color: var(--czvr-text);">
                <small>View Completed Students</small>
            </a>
        </div>

        <div class="row">
            @foreach($lists as $index => $list)
            <div class="col-lg-4">
                <section class="card p-3 shadow-none mb-3 rounded-card prechecks-card student-list-card">
                    <h2>
                        <i class="fa fa-circle fa-fw" style="color: {{$list->color}};"></i>&nbsp;
                        {{$list->name}} [{{count($list->students)}}]
                    </h2>
                    <div id="list-group-{{$index}}" class="list-group mt-3" style="max-height: 350px; overflow-y: auto;">
                        @if(count($list->students) == 0)
                            <i class="text-center" style="margin-top: 20px; color: var(--czvr-text-muted);">No Students for this List</i>
                        @else
                            @php($loopIndex = 1)
                            @foreach($list->students->sortBy(function($student) { return $student->student->user->fullName('FLC'); }) as $student)
                                <a href="{{ route('training.students.view', $student->student_id) }}" class="list-group-item rounded list-group-item-action waves-effect" style="background-color: transparent; color: var(--czvr-text); flex-shrink: 0;">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-wrap mb-1">
                                            @foreach($student->student->labels as $label)
                                                <span class="badge shadow-none label-hidden" style="background-color: {{$label->label->color}}; height: 8px; width: 32px; margin: 2px;">&nbsp;</span>
                                                <span class="badge shadow-none label-expanded" style="background-color: {{$label->label->color}}; display: none; font-size: 13px; font-weight: 400; margin: 2px;">@if ($label->label->fa_icon)<i class="{{ $label->label->fa_icon }} fa-fw me-1" aria-hidden="true"></i>@endif{{ $label->label->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="student-list-name">
                                            <span class="badge student-list-number me-1">{{$loopIndex}}</span>
                                            {{$student->student->user->fullName('FLC')}}

                                            @if($student->student->isConsolidating())
                                                <span class="badge text-bg-warning ms-1" style="font-size: var(--czvr-font-size-base);" title="Training unlocks when consolidation is complete">
                                                    {{ number_format($student->student->consolidationRemainingHours(), 1) }}h remaining
                                                </span>
                                            @endif

                                            @if(! $student->student->user->rosterProfile)
                                                <span class="badge text-bg-danger ms-2"
                                                      style="font-size: var(--czvr-font-size-base);"
                                                      title="User is not certified / not on roster">
                                                    NOT ON VATCAN
                                                </span>
                                            @endif
                                        </span>

                                        @if($student->student->instructor)
                                        <span class="fw-bold d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; font-size: var(--czvr-font-size-base); margin-top: -11px;"
                                            title="{{ $student->student->instructor->user->fullName('FLC') }}">
                                            {{ strtoupper(substr($student->student->instructor->user->fname, 0, 1)) }}
                                            {{ strtoupper(substr($student->student->instructor->user->lname, 0, 1)) }}
                                        </span>
                                        @else
                                            <span class="d-flex align-items-center justify-content-center align-self-start"
                                                style="width: 28px; height: 28px; font-size: var(--czvr-font-size-base); margin-top: -11px;"
                                                title="No Instructor!">!</span>
                                        @endif
                                    </div>
                                </a>
                                @php($loopIndex++)
                            @endforeach
                        @endif
                        <hr>
                    </div>
                </section>
            </div>
            @endforeach
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggleButton = document.getElementById('toggleLabels');
        function toggleLabels() {
            var labelsHidden = document.querySelectorAll('.label-hidden');
            var labelsExpanded = document.querySelectorAll('.label-expanded');
            labelsHidden.forEach(function(label) {
                label.style.display = label.style.display === 'none' ? 'inline-block' : 'none';
            });
            labelsExpanded.forEach(function(label) {
                label.style.display = label.style.display === 'none' ? 'inline-block' : 'none';
            });
            toggleButton.textContent = toggleButton.textContent === 'Hide labels' ? 'Expand labels' : 'Hide labels';
        }
        toggleButton.addEventListener('click', function(event) {
            event.preventDefault();
            toggleLabels();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('studentSearch');
        searchInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.list-group-item').forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
</script>

@stop
