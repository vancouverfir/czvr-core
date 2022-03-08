@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="blue-text font-weight-bold mt-2">Settings</h1>
        <hr>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Site information</h3>
                    <p>Manage the website version, copyright, etc.<br>&nbsp;</p>
                    <a class="white-text font-weight-bold" href="{{route('settings.siteinformation')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Emails</h3>
                    <p>Set staff email addresses.&nbsp;</p><br></br>
                    <a class="white-text font-weight-bold" href="{{route('settings.emails')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Audit Log</h3>
                    <p>Log of all Core events.<br>&nbsp;</p><br>
                    <a class="white-text font-weight-bold" href="{{route('settings.auditlog')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Homepage Images</h3>
                    <p>Add, remove or amend homepage images.<br>&nbsp;</p>
                    <a class="white-text font-weight-bold" href="{{route('settings.images')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Staff</h3>
                    <p>Who's on the team? Find out (and make changes) here.<br>&nbsp;</p>
                    <a class="white-text font-weight-bold" href="{{route('settings.staff')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>Banner</h3>
                    <p>Set a banner for maintenance, or other Winnipeg things.<br>&nbsp;</p>
                    <a class="white-text font-weight-bold" href="{{route('settings.banner')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 p-4 btn-primary shadow-none">
                    <h3>User Roles</h3>
                    <p>Edit User Roles for site permissions<br>&nbsp;</p>
                    <a class="white-text font-weight-bold" href="{{route('roles.view')}}">Go <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
@stop
