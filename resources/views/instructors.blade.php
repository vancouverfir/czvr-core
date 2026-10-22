@extends('layouts.master')

@section('title', 'Instructing Staff - Vancouver FIR')
@section('description', 'The Vancouver Instructors and Mentors!')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('staff')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Staff</a>
        <div class="col p-0">

            <h2 class="blue-text fw-bold">Instructors</h2>
            <div class="row">
                @foreach (\App\Models\Teacher::where('is_instructor', 1)->get() as $instructor)
                    <div class="col col-6 mb-3">
                        <div class="card card-body">
                            <div class="d-flex flex-row">
                                <img src="{{$instructor->user->avatar()}}" style="border-radius: 50%; width: 100px; height: 100px; margin-right: 4%;">
                                <div class="d-flex flex-column">
                                    <h3 class="mb-1 fw-bold blue-text">{{$instructor->user->fullName('FL')}}</h3>
                                    <!-- <a href="mailto:{{$instructor->user->email}}"><h5 class="blue-text">{{$instructor->user->email}}</h5></a> -->
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($instructor->is_local)
                                            <span class="qualification-badge qualification-local">Local</span>
                                        @endif
                                        @if($instructor->is_radar)
                                            <span class="qualification-badge qualification-radar">Radar</span>
                                        @endif
                                        @if($instructor->is_enroute)
                                            <span class="qualification-badge qualification-enroute">En-Route</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <br>

            <h2 class="blue-text fw-bold">Mentors</h2>
            <div class="row">
                @foreach (\App\Models\Teacher::where('is_instructor', 0)->get() as $instructor)
                    <div class="col col-6 mb-3">
                        <div class="card card-body">
                            <div class="d-flex flex-row">
                                <img src="{{$instructor->user->avatar()}}" style="border-radius: 50%; width: 100px; height: 100px; margin-right: 4%;">
                                <div class="d-flex flex-column">
                                    <h3 class="mb-1 fw-bold blue-text">{{$instructor->user->fullName('FL')}}</h3>
                                    <!-- <a href="mailto:{{$instructor->user->email}}"><h5 class="blue-text">{{$instructor->user->email}}</h5></a> -->
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($instructor->is_local)
                                            <span class="qualification-badge qualification-local">Local</span>
                                        @endif
                                        @if($instructor->is_radar)
                                            <span class="qualification-badge qualification-radar">Radar</span>
                                        @endif
                                        @if($instructor->is_enroute)
                                            <span class="qualification-badge qualification-enroute">En-Route</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <br>

        </div>
    </div>
    <br>
@stop
