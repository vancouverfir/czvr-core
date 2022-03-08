@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h2 class="font-weight-bold blue-text">Instructor Portal</h2>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Your Students</text>
                        </div>

                    <div class="card-body">
                        @if ($yourStudents !== null)
                        <div class="list-group">
                            @foreach ($yourStudents as $student)
                            <a href="{{route('training.students.view', $student->id)}}" class="list-group-item d-flex justify-content-between align-items-center">
                                {{$student->user->fullName('FLC')}}
                                {{-- <i class="text-dark">Session planned at {date}</i> --}}
                                @if ($student->status == 0)
                                <span class="btn-sm btn-success">
                                    <h6 class="p-0 m-0">
                                        Open
                                    </h6>
                                </span>
                                @elseif ($student->status == 4)
                                <span class="btn-sm btn-danger">
                                    <h6 class="p-0 m-0">
                                        On Hold
                                    </h6>
                                </span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        @else
                        No students are allocated to you.
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                @hasanyrole('Chief-Instructor|Administrator')
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Student Solo Requests</text>
                    </div>

                    <div class="card-body">
                    @if (count($soloreq) < 1)
                        There are currently no requests!
                    @else
                        @foreach ($soloreq as $s)
                        <text class="font-weight-bold">Student: </text> {{$s->student->user->fullName('FLC')}}<br>
                        <text class="font-weight-bold">Instructor: </text> {{$s->instructor->user->fullName('FLC')}}<br>
                        <text class="font-weight-bold">Solo Position: </text> {{$s->position}}<br>
                        <a href="{{route('training.solo.approve', $s->id)}}" class="ml-0 btn btn-success btn-sm" style="float: center;">Approve</a>
                        <a href="{{route('training.solo.deny', $s->id)}}" class="btn btn-danger btn-sm" style="float: center;">Deny</a>
                        <hr>
                        @endforeach
                    @endif
                    </div>

            </div>
                @endhasanyrole
            </div>
        </div>
        <br>

        <h3 class="font-weight-bold blue-text">Training Calendar</h3>
        <br>

    </div>
@stop
