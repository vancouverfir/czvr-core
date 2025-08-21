@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'ATC Resources')
@section('description', 'ATC Resources for Vancouver Controllers!')

@section('content')

@include('includes.trainingMenu')
<div class="container" style="margin-top: 20px;">
    <div class="container" style="margin-top: 20px;">
    <h1 class="blue-text font-weight-bold mt-2">ATC Resources</h1>
    <div class="list-group list-group-flush">
        @foreach ($resources as $resource)
        @continue($resource->atc_only && Auth::check() && !Auth::user()->rosterProfile)
        <div class="list-group-item">
            <div class="row">
                <div class="col"><b>{{$resource->title}}</b></div>
                <div class="col-sm-4">
                <a href="{{$resource->url}}" class="white-text" target="_blank"><i class="fa fa-eye"></i>&nbsp;View Resource</a>&nbsp;&nbsp;
                <a href="#" data-toggle="modal" class="white-text" data-target="#detailsModal{{$resource->id}}"><i class="fa fa-window-close"></i>&nbsp Delete Resource</a>
                </div>
            </div>
        </div>
        <div class="modal fade" id="detailsModal{{$resource->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{$resource->title}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <small>Description</small><br/>
                         {{$resource->toHtml('description')}}
                    </div>
                    <div class="modal-footer">
                        @if (Auth::check() && Auth::user()->permissions >= 3)
                        <a href="{{route('atcresources.delete', $resource->id)}}" role="button" class="btn btn-danger">Delete Resource</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <br/><br>
    @if (Auth::check() && Auth::user()->permissions >= 3)
    <form method="POST" action="{{route('atcresources.upload')}}">
        @csrf
        <h3 class="blue-text">Add Resource</h3>
        <div class="form-group">
            <p>Title</p>
            <input required class="form-control" type="text" name="title">
        </div>
        <div class="form-group">
            <p>Description</p>
            <textarea id="descriptionField" name="description" cols="30" rows="10" required></textarea>
            <script>
                var simplemde = new SimpleMDE({ element: document.getElementById("descriptionField") });
            </script>
        </div>
        <div class="form-group">
            <p>Link to Resource</p>
            <input type="url" class="form-control" name="url" required>
        </div>
        <div class="form-group">
            <div class="d-flex align-items-center">
                <span id="iconPreview" style="font-size: 1.5rem; color: #f1f1f1; margin-right: 8px;">
                    <i class="fa fa-cloud"></i>
                </span>
                <p class="mb-0">Icon</p>
            </div>
            <div class="d-flex align-items-center">
                <select id="iconSelect" name="font_awesome" class="form-control mr-3" required>
                    <option value="fa-cloud">Weather</option>
                    <option value="fa-headset">ATC</option>
                    <option value="fa-plane-departure">Notam</option>
                    <option value="fa-book">Document</option>
                    <option value="fa-map">Map</option>
                    <option value="fa-comments">Chat</option>
                    <option value="fa-graduation-cap">Training</option>
                    <option value="fa-exclamation-triangle">Alert</option>
                    <option value="fa-info-circle">Info</option>
                    <option value="fa-video">Video</option>
                    <option value="fa-file-alt">Other</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="hidden" name="atc_only" value="0">
                    <input type="checkbox" class="custom-control-input" name="atc_only" id="atc_only" value="1">
                    <label class="custom-control-label" for="atc_only">ATC Only</label>
                </div>
            </div>
        </div>
        <br/>
        <input value="Submit" type="submit" class="btn btn-block btn-success"><br></br>
    </form>
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const select = document.getElementById("iconSelect");
        const preview = document.getElementById("iconPreview").querySelector("i");

        select.addEventListener("change", function () {
            preview.className = "fa " + this.value;
        });
    });
</script>

@stop
