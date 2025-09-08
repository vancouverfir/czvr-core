@extends('layouts.master')

@section('title', 'Submit Feedback - Vancouver FIR')

@section('description', 'Submit feedback for our controllers')


<style>
    .select2-container--default .select2-selection--single {
        background-color: #333 !important;
        color: #fff !important;
        height: 30px !important;
        line-height: 30px !important;
        border-radius: 0 !important;
        border: 1px solid #555 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff !important;
    }
    .select2-dropdown {
    background-color: #333 !important;
    color: #fff !important;
    }
    .select2-search__field {
        background-color: #333 !important;
        color: #000000ff !important;
    }
</style>


@section('content')
    <div class="container py-4">
        <h1 class="font-weight-bold blue-text">Submit Feedback</h1>
        <p class="content-font-color" style="font-size: 1.2em;">
            Have feedback for the Vancouver FIR? This is the place to submit it!
        </p>
        <hr>
        @if($errors->createFeedbackErrors->any())
            <div class="alert alert-danger">
                <h4>Error</h4>
                <ul class="pl-0 ml-0" style="list-style:none;">
                    @foreach ($errors->createFeedbackErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('feedback.create.post')}}" method="POST">
            @csrf
            <ul class="mt-0 pt-0 pl-0 stepper stepper-vertical">
                <li class="active">
                    <a href="#!">
                        <span class="circle circle-color">1</span>
                        <span class="label content-font-color">Type of feedback</span>
                    </a>
                    <div class="step-content w-75 box-colour content-font-color">
                        <p>Please select the type of feedback you are submitting.</p>
                        <select name="feedbackType" id="feedbackTypeSelect" class="form-control">
                            <option value="0" hidden>Please select one...</option>
                            <option value="controller">Controller Feedback</option>
                            <option value="event">Event Feedback</option>
                            <option value="website">Website Feedback</option>
                        </select>
                    </div>
                </li>
                <li class="active">
                    <a href="#!">
                        <span class="circle circle-color">2</span>
                        <span class="label content-font-color">Your message</span>
                    </a>
                    <div id="typeNotSelected" class="step-content w-75 box-colour content-font-color">
                        Please select a feedback type first!
                    </div>
                    <div id="typeSelected" class="step-content w-75 box-colour content-font-color" style="display:none">
                        <div class="md-form" id="controllerCidGroup" style="display:none">
                            <div>
                                <p>Controller's Name/CID</p>
                            </div>
                            <select name="controllerCid" id="controllerCid" class="form-control">
                                <option id="0" value="0" hidden>Select a controller...</option>
                            @foreach($controllers as $c)
                            <option name="controllerName" value="{{ $c->cid }}" id="{{ $c->cid }}">
                                @if($c->user->fullName('FL') == $c->cid)
                                    {{$c->cid}}
                                @else
                                {{$c->user->fullName('FL')}} - {{$c->cid}}
                                @endif</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="md-form" id="positionGroup" style="display:none">
                            <p>Position</p>
                            <input type="name" name="position" class="form-control" placeholder="CZVR_CTR">
                        </div>
                        <div class="md-form" id="subjectGroup" style="display:none" placeholder="">
                            <p>Subject</p>
                            <input type="text" name="subject" class="form-control">
                        </div>
                        <div id="contentGroup">
                            <p>Your Feedback</p>
                            <textarea class="form-control" name="content" class="w-75"></textarea>
                        </div>
                    </div>
                </li>
            </ul>
            <button class="btn btn-success" style="font-size: 1.1em; font-weight: 600;"><i class="fas fa-check"></i>&nbsp;&nbsp;Submit Feedback</button>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        /*
        Show/hide message form based on whether the user has selected a feedback type
        */
        $("#feedbackTypeSelect").on('change', function() {
            if (this.value) {
                $("#typeNotSelected").hide();
                $("#typeSelected").show();
            }
        })

        /*
        Feedback type select to disable/enable the CID field and subject field
        */
        $('#feedbackTypeSelect').on('change', function() {
            //Figure out what it is
            if (this.value == 'controller') {
                //Enable CID disable subject
                $("#controllerCidGroup").show();
                $("#positionGroup").show();
                $("#subjectGroup").hide();
            } else {
                //Maybe not
                $("#controllerCidGroup").hide();
                $("#positionGroup").hide()
                $("#subjectGroup").show();

                if (this.value == 'event') {
                    $("#subjectGroup input[name='subject']").attr("placeholder", "Cross the Pond 2025, FNO, West Coast Weekends...");
                } else {
                    $("#subjectGroup input[name='subject']").attr("placeholder", "");
                }
            }
        })

        $(document).ready(function() {
            $('#controllerCid').select2({
                placeholder: "Select a controller...",
                width: '100%'
            });
        });
    </script>

@endsection
