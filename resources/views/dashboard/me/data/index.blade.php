@extends('layouts.master')
@section('content')
@section('title', 'Your Data - Vancouver FIR')
<div class="container py-4">
    <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
    <h1 class="blue-text font-weight-bold mt-2">Your Data</h1>
    <hr>
    <p>Under our <a href="{{route('privacy')}}">Privacy Policy</a>, you have the right to export and delete data from our service.</p>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Export Data
                </div>
                <div class="card-body">
                    To export your data, you may either:
                    <ul class="mt-0 pt-0 pl-0 stepper stepper-vertical">
                        <li class="active">
                          <a href="#!">
                            <span class="circle">1</span>
                            <span class="label content-font-color">Export all data</span>
                          </a>
                          <div class="step-content box-colour">
                            <p>To export all data, please email webmaster@czvr.ca</p>
                            {{--<form action="{{route('me.data.export.all')}}" method="POST">
                                @csrf
                                <div class="md-form">
                                    <input name="email" type="email" id="inputMDEx" class="form-control">
                                    <label for="inputMDEx">Email address</label>
                                </div>
                                @if($errors->exportAll->any())
                                <div class="alert alert-danger">
                                    <h4>Error</h4>
                                    <ul class="pl-0 ml-0">
                                        @foreach ($errors->exportAll->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if (Session::has('exportAll'))
                                <div class="alert alert-success pb-0">
                                    <p>{{Session::get('exportAll')}}</p>
                                </div>
                                @endif
                                <input type="submit" value="Request Data" class="btn btn-primary">
                            </form>--}}
                          </div> 
                        </li>
                        <li class="active">
                            <a href="#!">
                              <span class="circle">2</span>
                              <span class="label content-font-color">or export specific data</span>
                            </a>
                            <div class="step-content box-colour">
                              <p>Please email our websupport team to request specific pieces of data.</p>
                                <button onclick="location.href='mailto:webmaster@czvr.ca'" class="btn btn-primary">Email our Support</button>
                            </div>
                          </li>
                      </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Delete Data</div>
                <div class="card-body">
                    <p>Please email our websupport team to request deletion of your data.</p>
                    <button onclick="location.href='mailto:webmaster@czvr.ca'" class="btn btn-primary">Email our Support</button>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Email Preferences</div>
                <div class="card-body">
                    <p>To edit your email preferences, visit the <a href="{{route('me.preferences')}}">preferences page.</a></p>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Questions and Concerns</div>
                <div class="card-body">
                    <p>If you have a question related to data management, please contact the Web Team.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
