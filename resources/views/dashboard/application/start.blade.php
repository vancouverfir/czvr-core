@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px;">
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('application.list')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Applications</a>
        <h1 class="blue-text font-weight-bold mt-2">Visiting Application</h1>
        <hr>
        <script>
            function countChar(val) {
                var len = val.value.length;
                if (len > 550){
                    $('#charNum').text(len + ' characters (Too many)');
                }
                else if (len < 100){
                    $('#charNum').text(len + ' characters (Too little)');
                }else {
                    $('#charNum').text(len + ' characters');
                }
            }
        </script>

        @if ($allowed == 'true')
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">You are eligible to apply!</h4>
                <p class="font-weight-bold">Vancouver is currently pausing all training for visiting controllers as we are busy with home students. However, we are always accepting applications to visit! One of our staff members will be in touch when we are able to accommodate your training. Thank you for your patience.</p>
                <p>Please note the following is also required to visit Vancouver:</p>
                <ul>
                    <li>Approval by your home FIR/vACC/ARTCC Chief/ATM.</li>
                </ul>
            </div>

            @if ($errors->applicationErrors->any())
                <div class="alert alert-danger">
                    <h4 class="alert-heading">One or more errors occurred while attempting to submit your application.</h4>
                    <ul>
                        @foreach ($errors->applicationErrors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{route('application.submit')}}" method="POST">
                @csrf
                <ul class="mt-0 pt-0 pl-0 stepper stepper-vertical">
                    <li class="active">
                        <a href="">
                            <span class="circle">1</span>
                            <span class="label">Why would you like to control in Vancouver?</span>
                        </a>
                        <div class="step-content w-75 grey lighten-3">
                            <p>Please type here (minimum 50 words):</p>
                            <textarea name="applicant_statement" class="w-100" id="justificationField" onkeyup="countChar(this)"></textarea>
                            <script>
                                var simplemde = new SimpleMDE({ element: document.getElementById("justificationField"), toolbar:false });
                            </script>
                        </div>
                    </li>

                    <li class="active">
                        <a href="">
                            <span class="circle">2</span>
                            <span class="label">Finish your application</span>
                        </a>
                        <div class="step-content w-75 grey lighten-3">
                            <h5>Activity requirements</h5>
                            <p>By applying to be a visitor in the Vancouver FIR, you acknowledge that you agree to abide by the Vancouver FIR General Policies, Standard Operating Procedures, and any other relevant FIR documents as a controller.</p>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" required name="agreeActivity" id="agreeActivity">
                                <label class="custom-control-label" for="agreeActivity">I Understand</label>
                            </div>
                        </div>
                    </li>
                </ul>

                <button type="submit" class="btn btn-success">Submit Your Application</button>
            </form>

        @elseif ($allowed == "false")
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">You are not eligible to apply.</h4>
                <p>You are not yet a S2 controller or higher. Please check back when you have a S2 rating.</p>
                <p>We look forward to working with you!<br>- Vancouver FIR</p>
                <p>If you believe there is an error, please <a href="{{route('tickets.index', ['create' => 'yes', 'department' => 'firchief', 'title' => 'Issue with requirement check on application system'])}}">start a support ticket.</a></p>
            </div>

        @elseif ($allowed == "pendingApplication")
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">You already have another pending application.</h4>
                <p>Please wait for this application to be processed. Processing times may take up to 48 hours.</p>
                <p>If you believe there is an error, please <a href="{{route('tickets.index', ['create' => 'yes', 'department' => 'firchief', 'title' => 'Issue with pending check on application system'])}}">start a support ticket.</a></p>
            </div>

        @else
            <b>You are not eligible to apply, but we're not sure why. Please contact the FIR Chief for further assistance.</b>
        @endif
    </div>
</div>
@stop
