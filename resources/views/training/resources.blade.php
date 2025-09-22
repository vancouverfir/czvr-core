@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<style>
    hr.bg-light {
        border-top: 1px solid #f8f9fa;
        width: 100%;
    }
    .card {
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(255, 255, 255, .3);
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(255, 255, 255, .3);
    }
</style>

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    @if (Auth::user()->permissions >= 1)
        <div class="resource-card border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="font-weight-bold mb-0"> <i class="fas fa-folder-open me-2 blue-text"> </i> ATC Resources </h1>
                    @if(Auth::user()->permissions >= 3)
                        <a href="{{ route('atcresources.index') }}" class="btn btn-sm btn-outline-white"> Manage Resources </a>
                    @endif
                </div>
                    <h3>
                       Click <a href="{{ url('https://vatcan.ca/my/cbt/CZVR')}}" target="_blank" class="blue-text"> <u>here</u> </a> to go to CZVR's CBTs on Vatcan
                    </h3>
                <hr class="bg-light">

                <div class="row">
                    @forelse($atcResources as $resource)
                        @if($resource->atc_only && Auth::user()->permissions < 1)
                            @continue
                        @endif
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-1 resource-card d-flex flex-column" style="border-color: white;">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <div class="mb-3">
                                        <i class="fas {{ $resource->font_awesome }} fa-3x text-info"></i>
                                    </div>

                                    <h5 class="card-title">{{ $resource->title }}</h5>
                                    <hr class="bg-light">
                                    <h5 class="card-title">{{ $resource->description }}</h5>

                                    <div class="mt-auto w-100">
                                        <a href="{{ $resource->url }}" target="_blank" class="btn btn-sm btn-outline-light w-100">
                                            View Resource
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="px-3">No Resources!</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>

@stop
