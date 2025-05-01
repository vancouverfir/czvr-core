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
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<div class="container swirly-background" style="margin-top: 20px;">
    <div class="content">
        <h1 class="font-weight-bold text-light">
            Students
            <a href="#" data-toggle="modal" data-target="#newStudent" class="text-primary" style="font-size: 18px; text-decoration: none; float: right;">
                <i class="fa fa-plus mr-1"></i>Add New Student
            </a>
        </h1>

        <hr class="bg-light">
        <h5><u><a href="#" id="toggleLabels" style="text-decoration: none; color: #fff;">Expand Labels</a></u></h5>

        <div class="row">
            @foreach($lists as $index => $list)
            <div class="col-lg-4">
                <div class="card p-3 z-depth-1 shadow-none mb-3 rounded-card prechecks-card" style="min-height: 160px;">
                    <h5>
                        <i class="fa fa-circle fa-fw" style="color: {{$list->color}};"></i>&nbsp;
                        {{$list->name}} ({{count($list->students)}})
                    </h5>
                    <div id="list-group-{{$index}}" class="list-group mt-3" style="max-height: 350px; overflow-y: auto;">
                        @if(count($list->students) == 0)
                            <i class="text-center text-light" style="margin-top: 20px;">No Students for this List</i>
                        @else
                            @php($loopIndex = 1)
                            @foreach($list->students as $student)
                                <a href="{{url('/dashboard/training/students/' . $student->student_id)}}" class="list-group-item rounded list-group-item-action waves-effect text-light" style="background-color: transparent; flex-shrink: 0;">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-wrap mb-1">
                                            @foreach($student->student->labels as $label)
                                                <span class="badge shadow-none label-hidden" style="background-color: {{$label->label->color}}; height: 8px; width: 32px; margin: 2px;">&nbsp;</span>
                                                <span class="badge shadow-none label-expanded" style="background-color: {{$label->label->color}}; display: none; font-size: 13px; margin: 2px;">{{$label->label->labelHtml()}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <p class="h3">
                                        <span class="badge badge-hidden mr-1">{{$loopIndex}}</span>
                                            {{$student->student->user->fullName('FLC')}}
                                        </p>
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
    $('#student_id').select2({
        width: '100%',
        placeholder: "Select a Student"
    });

    $('#instructor').select2({
        width: '100%',
        placeholder: "Assign an Instructor",
        allowClear: true
    });
});
</script>

<div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>       
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('instructor.student.add.new') }}" class="form-group">
                    @csrf
                    <label class="form-control font-weight-bold">Search for a Student</label>
                    <select name="student_id" id="student_id" class="js-example-basic-single form-control" style="width:100%;">
                        <option value="">No Student</option>
                        @foreach ($potentialstudent as $u)
                            <option value="{{ $u->id }}">{{ $u->id }} - {{ $u->fullName('FL') }}</option>
                        @endforeach
                    </select>
                    <label class="form-control font-weight-bold">Assign an Instructor?</label>
                    <select name="instructor" id="instructor" class="js-example-basic-single form-control" style="width:100%;">
                        <option value="">No Instructor</option>
                        @foreach ($instructors as $i)
                            <option value="{{ $i->id }}">{{ $i->user->id }} - {{ $i->user->fullName('FL') }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success form-control" type="submit">Add Student</button>
                <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                </form>
            </div>
        </div>
    </div>
</div><br>

@stop
