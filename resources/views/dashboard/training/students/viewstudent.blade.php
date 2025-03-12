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
                                    {{$student->user->fullName('FLC')}}
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
                        <h5 class="mt-3 font-weight-bold">Assigned Instructor</h5>
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
                <div class="card">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Instructing Sessions</h3>
                        @if (count($student->instructingSessions) >= 1)
                        @else
                            None found!
                        @endif
                    </div>
                </div>
            <br>
          </div>
          <div class="col"> 
                <div class="card">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Info</h3>
                            <h7 class="list-group-item" style="background: transparent;"><a href="{{ url('/roster/' . $student->user->id) }}" style="color: inherit;">&#x2192; View their roster profile here</a></h7>
                            <h7 class="list-group-item" style="background: transparent;" >Joined {{$student->created_at?->format('j F Y') ?? 'N/A'}} | Rating {{$student->user->rating_short}}</h7>
                            @if ($student->instructor)
                                <h7 class="list-group-item" style="background: transparent; color: #ff0000; cursor: pointer;" data-toggle="modal" data-target="#confirmRemoveInstructorModal">Remove Instructor</h7>
                            @else
                                <h7 class="list-group-item" style="background: transparent; color: #2cb82c; cursor: pointer;" data-toggle="modal" data-target="#assignInstructorModal">Assign Instructor</h7>
                            @endif
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
                                  @foreach ($student->trainingNotes as $notes)
                                  <tr>
                                      <th scope="row"><a class="font-weight-bold blue-text" href="{{route('trainingnote.view', $notes->id)}}">{{$notes->title}}</a></th>
                                      <td>
                                        {{$notes->created_at}}
                                      </td>
                                      <td>                     
                                        {{$notes->instructor->user->fullName('FLC')}}
                                      </td>
                                  </tr>
                                  @endforeach
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

<!-- Confirm Remove Instructor Modal -->
<div class="modal fade" id="confirmRemoveInstructorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Instructor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove the assigned instructor?
            </div>
            <div class="modal-footer">
                <form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="instructor" value="unassign">
                    <button type="submit" class="btn btn-danger">Yes</button>
                </form>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
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
</script>
@stop
