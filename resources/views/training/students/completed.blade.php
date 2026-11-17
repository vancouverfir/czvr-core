@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
@include('partials.vendor.datatables')
    @include('includes.trainingMenu')

    <main class="container training-page">
        <header class="training-page-header">
            <div>
                <a href="{{route('training.students.students')}}" class="small"><i class="fas fa-arrow-left me-1"></i>Students</a>
                <h1 class="blue-text">Completed Students</h1>
            </div>
        </header>
        <div class="training-surface p-3 table-responsive">
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Type</th>
                </tr>
            </thead>
            <tbody>
                @if (count($students) < 1)
                    <tr>
                        <td colspan="3" class="fw-bold text-center">
                            There are no students in this category!
                        </td>
                    </tr>
                @else
                    @foreach ($students as $student)
                        <tr>
                            <th scope="row">{{ $student->user->id }}</th>
                            <td>
                                <a href="{{ route('training.students.view', $student->id) }}" class="blue-text">
                                    {{ $student->user->fullName('FL') }}
                                </a>
                            </td>
                            <td>
                                {{ $student->user->visitor ? 'Visitor' : 'Home' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </main>
@stop
