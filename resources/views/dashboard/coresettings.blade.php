@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <div class="container" style="margin-top: 20px;">
        <h2>Core Settings</h2>
        <br/>
        <form action="{{route('coresettings.store')}}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <th scope="col">Variable</th>
                    <th scope="col">Value</th>
                </thead>
                <tbody>
                    <tr>
                        <th scope="col">System Name</th>
                        <td>
                            <input type="text" name="sys_name" value="{{old('sys_name', $settings->sys_name)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Release</th>
                        <td>
                            <input type="text" name="release" value="{{old('release', $settings->release)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Build</th>
                        <td>
                            <input type="text" name="sys_build" value="{{old('sys_build', $settings->sys_build)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Copyright Year</th>
                        <td>
                            <input type="text" name="copyright_year" value="{{old('copyright_year', $settings->copyright_year)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col"><i>Emails</i></th>
                    </tr>
                    <tr>
                        <th scope="col">FIR Chief</th>
                        <td>
                            <input type="text" name="emailfirchief" value="{{old('emailfirchief', $settings->emailfirchief)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Dep. FIR Chief</th>
                        <td>
                            <input type="text" name="emaildepfirchief" value="{{old('emaildepfirchief', $settings->emaildepfirchief)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Chief Instructor</th>
                        <td>
                            <input type="text" name="emailcinstructor" value="{{old('emailcinstructor', $settings->emailcinstructor)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Event Coordinator</th>
                        <td>
                            <input type="text" name="emaileventc" value="{{old('emaileventc', $settings->emaileventc)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Facility Engineer</th>
                        <td>
                            <input type="text" name="emailfacilitye" value="{{old('emailfacilitye', $settings->emailfacilitye)}}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Webmaster</th>
                        <td>
                            <input type="text" name="emailwebmaster" value="{{old('emailwebmaster', $settings->emailwebmaster)}}" class="form-control">
                        </td>
                    </tr>
                </tbody>
            </table>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h4 class="alert-heading">There were errors submitting</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>

        <hr/>
        <h2>Maintenance Mode Exempt IPs</h2>
        <table class="table">
            <thead>
                <th scope="col">Label</th>
                <th scope="col">IP Address (IPv4)</th>
                <th scope="col">Delete</th>
            </thead>
            <tbody>
                @foreach ($ips as $i)
                <tr>
                    <td>{{$i->label}}</td>
                    <td>{{$i->ipv4}}</td>
                    <td>
                        <a href="{{route('coresettings.exemptips.delete', $i->id)}}" class="red-text">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <h5>Add IP Address</h5>
        <form action="{{route('coresettings.exemptips.add')}}" method="POST">
            @csrf
            <div class="md-form input-group mb-3">
                <input type="text" name="label" class="form-control" placeholder="Label (e.g. FIR Chief)">
                <input type="text" class="form-control" placeholder="IP Address (e.g. 192.168.1.1)" name="ipv4">
                <div class="input-group-append">
                    <input value="Add" type="submit" class="btn btn-success">
                </div>
            </div>
        </form>
        <hr/>
        <a href="{{url('/nickxenophonssabest')}}">Webmaster Portal</a>
    </div>
@stop
