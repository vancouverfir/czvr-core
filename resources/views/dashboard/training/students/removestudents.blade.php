
@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <div class="card">
            <div class="card-body">
                <h3 class="font-weight-bold blue-text">Remove Student</h3>
                <p>Are you sure you want to remove <strong>{{ $student->user->fullName('FLC') }}</strong>?</p>
                <p class ="content-warning">This will permanently delete this student and their application!<p>
                <form action="{{ route('training.students.destroy', $student->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                    <a href="{{ route('training.students.waitlist') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@stop
