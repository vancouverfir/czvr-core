@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<style>
    .rounded-card {
        border-radius: 15px !important;
        overflow: hidden;
    }
    .swirly-background {
        position: relative;
        overflow: hidden;
        padding: 20px;
        background: #1e1e1e;
    }
    .swirly-background::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.1) 25%, rgba(255, 255, 255, 0.05) 50%, rgba(255, 255, 255, 0.1) 75%);
        background-size: 100px 100px;
        opacity: 0.2;
        pointer-events: none;
        z-index: 1;
        transform: rotate(-30deg);
    }
    .list-group-item {
        margin: 3px 0;
        border: 1px solid gray;
        border-radius: 8px;
        padding: 8px 12px;
    }
    .select2-container--default .select2-selection--single {
        background-color: #333 !important;
        color: #fff !important;
        border: 1px solid #777 !important;
    }
    .select2-dropdown {
    background-color: #333 !important;
    color: #fff !important;
    }
    .select2-search__field {
        background-color: #333 !important;
        color: #fff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff !important;
    }
    .select2-container--default .select2-selection__clear {
        color: red !important;
        font-size: 16px;
    }
    #studentSearch::placeholder {
        color: white;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<div class="container swirly-background" style="margin-top: 30px; margin-bottom: 30px">
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="font-weight-bold blue-text mb-0">Students</h1>
            <h5><u><a href="#" id="toggleLabels" style="text-decoration: none; color: #fff;">Expand Labels</a></u></h5>
        </div>

        <div class="form-group mb-2">
            <input type="text" class="form-control" id="studentSearch" placeholder="Search Students...">
        </div>
        <div class="mb-4">
            <a href="{{ route('training.students.completed') }}" class="blue-text" style="text-decoration: none; color: #fff;">
                <small>View Completed Students</small>
            </a>
        </div>

        <div class="row">
            @foreach($lists as $index => $list)
            <div class="col-lg-4">
                <div class="card p-3 z-depth-1 shadow-none mb-3 rounded-card prechecks-card" style="min-height: 160px;">
                    <h5>
                        <i class="fa fa-circle fa-fw" style="color: {{$list->color}};"></i>&nbsp;
                        {{$list->name}} [{{count($list->students)}}]
                    </h5>
                    <div id="list-group-{{$index}}" class="list-group mt-3" style="max-height: 350px; overflow-y: auto;">
                        @if(count($list->students) == 0)
                            <i class="text-center text-light" style="margin-top: 20px;">No Students for this List</i>
                        @else
                            @php($loopIndex = 1)
                            @foreach($list->students->sortBy(function($student) { return $student->student->user->fullName('FLC'); }) as $student)
                                <a href="{{url('/training/students/' . $student->student_id)}}" class="list-group-item rounded list-group-item-action waves-effect text-light" style="background-color: transparent; flex-shrink: 0;">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-wrap mb-1">
                                            @foreach($student->student->labels as $label)
                                                <span class="badge shadow-none label-hidden" style="background-color: {{$label->label->color}}; height: 8px; width: 32px; margin: 2px;">&nbsp;</span>
                                                <span class="badge shadow-none label-expanded" style="background-color: {{$label->label->color}}; display: none; font-size: 13px; margin: 2px;">{{$label->label->labelHtml()}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="h3 mb-0">
                                            <span class="badge badge-hidden mr-1">{{$loopIndex}}</span>
                                            {{$student->student->user->fullName('FLC')}}
                                        </p>

                                        @if($student->student->instructor)
                                        <span class="font-weight-bold text-white d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; margin-top: -11px;"
                                            title="{{ $student->student->instructor->user->fullName('FLC') }}">
                                            {{ strtoupper(substr($student->student->instructor->user->fname, 0, 1)) }}
                                            {{ strtoupper(substr($student->student->instructor->user->lname, 0, 1)) }}
                                        </span>
                                        @else
                                            <span class="text-white d-flex align-items-center justify-content-center align-self-start"
                                                style="width: 28px; height: 28px; font-size: 0.8rem; margin-top: -11px;"
                                                title="No Instructor!">!</span>
                                        @endif
                                    </div>
                                </a>
                                @php($loopIndex++)
                            @endforeach
                        @endif
                        <hr>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

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
            toggleButton.textContent = toggleButton.textContent === 'Hide Labels' ? 'Expand Labels' : 'Hide Labels';
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
