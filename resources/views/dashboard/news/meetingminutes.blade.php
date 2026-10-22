@extends('layouts.master')

@section('navbarprim')

    @parent

@stop
@section('title', 'Meeting Minutes - Vancouver FIR')
@section('description', 'View Vancouver FIR staff meeting minutes')
@section('content')
    <div class="container" style="margin-top: 1%">
        <h1 class="fw-bold blue-text">Meeting Minutes</h1>
        <br>
        @if (count($minutes) >= 1)
        <table class="table border-none">
            @foreach ($minutes as $m)
            <tr class="mb-0">
                <td>{{$m->title}}</td>
                @can('meeting-minutes.manage')
                <td>Added by {{\App\Models\Users\User::find($m->user_id)->fullName('FLC')}}</td>
                @endcan
                <td>
                <a target="_blank" href="{{$m->link}}"><i class="fa fa-eye"></i>&nbsp;View</a>
                </td>
                @can('meeting-minutes.manage')
                <td>
                <form method="POST" action="{{route('meetingminutes.delete', $m->id)}}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="border-0 bg-transparent p-0" style="color: red;"><i class="fa fa-times"></i>&nbsp;Delete</button>
                </form>
                </td>
                @endcan
            </tr>
            @endforeach
        </table>
        @else
        No Meeting Minutes Available.
        @endif
        @can('meeting-minutes.manage')

        <a href="#" data-bs-toggle="modal" class="btn btn-primary" data-bs-target="#upload">Upload Minutes</a><br></br>
        <div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Upload</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{route('meetingminutes.upload')}}" enctype="multipart/form-data">
                                @csrf
                                <label>Title</label>
                                    <input type="text" name="title" class="form-control">
                                        <br/>
                                    <input type="file" name="file" class="form-control">
                                        <br/>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-success" value="Upload">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@stop
