@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')

<div class="container py-4">
    <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
    <h1 class="blue-text font-weight-bold mt-2">Manage Uploaded Files Here!</h1>
    <hr>
    <div class="row">
        @if(!empty($files) && count($files) > 0)
            @foreach($files as $file)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-center">{{ $file }}</h5>
                            <hr>
                            <div class="file-preview d-flex justify-content-center align-items-center" style="flex-grow: 1;">
                                @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $file))
                                    <img src="{{asset('storage/files/uploads/' . $file)}}" alt="{{$file}}" class="img-fluid" style="max-width: 210px; max-height: 297px; object-fit: contain;">
                                @elseif(preg_match('/\.(pdf)$/i', $file))
                                    <div style="overflow: auto; height: 297px; width: 210px;">
                                        <embed src="{{asset('storage/files/uploads/' . $file)}}" type="application/pdf" style="width: 100%; height: 100%;">
                                    </div>
                                @else
                                    <i class="fas fa-file" style="font-size: 4em;"></i>
                                @endif
                            </div>
                            <div class="mt-2 text-center">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewModal{{$loop->index}}">View</button>
                                <a href="#" data-toggle="modal" data-target="#deleteModal{{$loop->index}}" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start View Upload Modal -->
                <div class="modal fade" id="viewModal{{$loop->index}}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">View File: {{ $file }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body d-flex justify-content-center align-items-center">
                                @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $file))
                                    <img src="{{asset('storage/files/uploads/' . $file)}}" alt="{{$file}}" class="img-fluid" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                                @elseif(preg_match('/\.(pdf)$/i', $file))
                                    <div style="overflow: auto; height: 80vh; width: 100%;">
                                        <embed src="{{asset('storage/files/uploads/' . $file)}}" type="application/pdf" style="width: 100%; height: 100%;">
                                    </div>
                                @else
                                    <i class="fas fa-file" style="font-size: 4em;"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End View Upload Modal -->

                <!-- Start Delete Upload Modal -->
                <div class="modal fade" id="deleteModal{{$loop->index}}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete File</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST" action="{{route('dashboard.uploaddelete', $file)}}">
                                @csrf
                                <div class="modal-body">
                                    <p class="font-weight-bold">Are you sure you wish to delete {{ $file }}? This action is irreversible!</p>
                                    <input type="hidden" name="filename" value="{{ $file }}">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Delete Upload Modal -->
            @endforeach
        </div>
        <hr>
        @else
            <p>No files found in the uploads folder.</p>
        @endif
    </div>
</div>
@stop
