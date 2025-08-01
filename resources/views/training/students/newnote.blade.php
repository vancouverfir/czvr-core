@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <a href="{{route('training.students.view', $student->id)}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Student </a>
        <div class="container" style="margin-top: 20px;">
            <h1 class="blue-text font-weight-bold"> New Staff Comment for {{$student->user->fullName('FLC')}} </h1>
            <hr>
            <form method="POST" action="{{ route('add.trainingnote', $student->id) }}" class="form-group">
                @csrf

                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>

                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control" required></textarea><br>

                <script>
                    tinymce.init({
                        selector: '#content',
                        menubar: false,
                        setup: function (ed) {
                            ed.on('blur', function (e) {
                                showSaveButton();
                            });
                        },
                    });
                </script>

                <input type="submit" class="btn btn-success" value="Add Staff Comment">
            </form>
        </div>
    </div>
@stop
