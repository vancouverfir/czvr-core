<?php $i = 1; ?>

@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
    @include('includes.cbtMenu')
    
    @if(Auth::check())
        <div class="container" style="margin-top: 20px;">
            <h2 class="font-weight-bold blue-text">
                Editing {{$module->name}}:
                @if (Auth::user()->permissions >= 4)
                    <hr>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-grey" 
                        data-toggle="modal" 
                        data-target="#moduleDetails" 
                        style="float: right;"
                    >
                        Edit Module Details
                    </button>
                    <a 
                        href="{{route('cbt.module.assignall', $module->id)}}" 
                        class="btn btn-success btn-sm" 
                        style="float: right;"
                    >
                        Assign to ALL Students
                    </a>
                    <a 
                        href="{{route('cbt.module.unassignall', $module->id)}}" 
                        class="btn btn-danger btn-sm" 
                        style="float: right;"
                    >
                        Unassign ALL Students
                    </a>
                    <br>
                @endif
            </h2>
            <br>

            <div align="center">
                {{-- Introduction Card --}}
                <div class="card p-3">
                    <h4 class="font-weight-bold" style="margin-bottom:0%">
                        Introduction - {{$intro->name}}
                    </h4>
                    <p>{!! $intro->toHtml('content_html') !!}</p>
                    @if (Auth::user()->permissions >= 4)
                        <div class="col" style="padding-bottom: 3.5%">
                            <a 
                                href="{{route('cbt.lesson.edit', $intro->id)}}" 
                                class="btn btn-primary btn-sm" 
                                style="width: 75px;"
                            >
                                Edit
                            </a>
                        </div>
                    @endif
                </div>
                
                <br><hr><br>

                {{-- Lessons --}}
                @foreach ($lessons as $l)
                    <div class="card p-3">
                        <h4 class="font-weight-bold" style="margin-bottom:0%">
                            Lesson {{$i}} - {{$l->name}}
                        </h4>
                        <p>{!! $l->toHtml('content_html') !!}</p>
                        @if (Auth::user()->permissions >= 4)
                            <div class="col" style="padding-bottom: 3.5%">
                                <a 
                                    href="{{route('cbt.lesson.edit', $l->id)}}" 
                                    class="btn btn-primary btn-sm" 
                                    style="width: 75px;"
                                >
                                    Edit
                                </a>
                                <a 
                                    href="{{route('cbt.lesson.delete', $l->id)}}" 
                                    class="btn btn-danger btn-sm" 
                                    style="width: 100px;"
                                >
                                    Delete
                                </a>
                            </div>
                        @endif
                        <?php $i++; ?>
                    </div>
                    <br><br>
                @endforeach

                {{-- Add Lesson Form --}}
                <form action="{{route('cbt.lesson.add', $module->id)}}" method="POST">
                    @csrf
                    <input type="hidden" name="lesson" value="lesson{{$i}}">
                    <button type="submit" class="btn btn-success" style="float: center;">
                        Add Lesson #{{$i}}
                    </button>
                </form>

                <br><hr><br>

                {{-- Conclusion Card --}}
                <div class="card p-3">
                    <h4 class="font-weight-bold" style="margin-bottom:0%">
                        Conclusion - {{$conclusion->name}}
                    </h4>
                    <p>{!! $conclusion->toHtml('content_html') !!}</p>
                    @if (Auth::user()->permissions >= 4)
                        <div class="col" style="padding-bottom: 3.5%">
                            <a 
                                href="{{route('cbt.lesson.edit', $conclusion->id)}}" 
                                class="btn btn-primary btn-sm" 
                                style="width: 75px;"
                            >
                                Edit
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <br>
        </div>

        {{-- Module Details Modal --}}
        <div 
            class="modal fade" 
            id="moduleDetails" 
            tabindex="-1" 
            role="dialog" 
            aria-labelledby="moduleDetailsTitle" 
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Module Details</h5>
                    </div>
                    <form method="POST" action="{{route('cbt.edit.moduledetails', $module->id)}}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Module Name</label>
                                <input 
                                    class="form-control" 
                                    name="name" 
                                    value="{{$module->name}}"
                                >
                            </div>
                            <div class="form-group">
                                <label>Exam Assign</label>
                                <select class="form-control" name="exam">
                                    <option 
                                        value="0" 
                                        {{ $module->cbt_exam_id == NULL ? "selected=selected" : ""}}
                                    >
                                        No Exam
                                    </option>
                                    @foreach ($exam as $exam)
                                        <option 
                                            value="{{$exam->id}}" 
                                            {{ $module->cbt_exam_id == $exam->id ? "selected=selected" : ""}}
                                        >
                                            {{$exam->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button 
                                class="btn btn-success form-control" 
                                type="submit" 
                                style="width:60%"
                            >
                                Save Changes
                            </button>
                            <button 
                                class="btn btn-light" 
                                data-dismiss="modal" 
                                style="width:40%"
                            >
                                Dismiss
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

