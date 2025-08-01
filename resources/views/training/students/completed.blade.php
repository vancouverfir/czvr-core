@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')

    <div class="container" style="margin-top: 20px;">
    <a href="{{route('training.students.students')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Students</a>
        <hr>
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Student Name</th>
                </tr>
            </thead>
            <tbody>
            @if (count($students) < 1)
                <tr>
                    <td colspan="3" class="font-weight-bold text-center">
                        There are no students in this category!
                    </td>
                </tr>
            @else
                @foreach ($students as $student)
                <tr>
                    <th scope="row">{{$student->user->id}}</th>
                    <td>
                        <a href="{{route('training.students.view', $student->id)}}" class="blue-text">
                            {{$student->user->fullName('FL')}}
                        </a>
                    </td>
                </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
@stop
