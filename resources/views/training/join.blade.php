@extends('layouts.master')
@section('title', 'Join - Vancouver FIR')
@section('description', 'Join the Vancouver FIR!')

@push('styles')
    <link href="{{ asset('css/pages/training.css') }}" rel="stylesheet">
@endpush

@section('content')
<main class="container py-4 join-page">
    <div class="page-heading">
        <h1 class="blue-text">Welcome to Vancouver!</h1>
        <p>We're home to VATSIM's most modern training program, some of the best instructors around and a great controlling environment! Interested in joining? We're happy to help out!</p>
    </div>

    <ul class="nav nav-tabs page-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab" aria-controls="new" aria-selected="true">If You're New to VATSIM</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit" type="button" role="tab" aria-controls="visit" aria-selected="false">If You Have a Home FIR/ARTCC/vACC</button>
        </li>
    </ul>

    <div class="tab-content join-panel">
        <section class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new-tab" tabindex="0">
            <div class="join-step">
                <h2>Step 1 – Join VATSIM</h2>
                <ol>
                    <li>If you are currently not a member of VATSIM then you can sign up for an account at the <a href="http://cert.vatsim.net/vatsimnet/signup.html" target="_blank" rel="noopener noreferrer">VATSIM Registration Page</a>. Ensure that you read all of the pages and instructions carefully.</li>
                    <li>Under the preferred region field select the <strong>Americas</strong> region and the <strong>Canada</strong> division.</li>
                    <li>After you complete your registration you receive an ID and password from VATSIM via email. When you have these two items, continue to step two.</li>
                </ol>
            </div>

            <div class="join-step">
                <h2>Step 2 – Join VATCAN &amp; Complete your S1 Exam</h2>
                <ol>
                    <li>You will need to join the VATCAN division. If you have not already joined, or skipped some steps from Step 1, click the following link to set your region: <a href="https://cert.vatsim.net/vatsimnet/regch.php" target="_blank" rel="noopener noreferrer">https://cert.vatsim.net/vatsimnet/regch.php</a>. You may proceed to step 5 if you are already a part of VATCAN.</li>
                    <li>You will now get your choice to select a region and division to join. To join VATCAN select <strong>Americas</strong> as your region.</li>
                    <li>In the select a division drop down box, select <strong>Canada</strong>.</li>
                    <li>Press <strong>Continue</strong> to set your selection.</li>
                    <li>Follow the steps on <a href="https://vatcan.ca/How-to-Become-a-Controller" target="_blank" rel="noopener noreferrer">https://vatcan.ca/How-to-Become-a-Controller</a> (if you already have your S1 (or higher) rating, you may skip step 3 on this page.)</li>
                </ol>
            </div>

            <div class="join-step">
                <h2>Step 3 – Join the Vancouver FIR</h2>
                <ol>
                    <li><a href="https://vatcan.ca/login" target="_blank" rel="noopener noreferrer">Login into VATCAN</a> using your VATSIM CID and password.</li>
                    <li>Click on <strong>My VATCAN</strong>, then on <strong>Transfer Request</strong>.</li>
                    <li>In the <strong>New FIR</strong> drop-down, select <strong>Vancouver FIR</strong>.</li>
                    <li>Fill in your reasoning, and hit <strong>Submit</strong>.</li>
                    <li>Your transfer will be reviewed by our FIR staff within 1-2 weeks.</li>
                </ol>
            </div>
        </section>

        <section class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab" tabindex="0">
            <p class="page-note join-note"><strong>Note:</strong> Vancouver is currently busy with a home controller wait list - this may result in lengthy delays for visiting controllers awaiting training! Controllers will be contacted with updates when available! Thank you for your patience!</p>
            <div class="join-step">
                <p>Vancouver is always looking for controllers to visit our FIR! Whether it's for a change of scenery, to learn a new way of controlling, or just for fun, come visit Vancouver!</p>
                <p>Members who are interested in visiting are now asked to visit the VATCAN.CA website to apply for visiting status!</p>
                <a class="btn btn-success" href="https://vatcan.ca/my/visit" target="_blank" rel="noopener noreferrer">Apply Now!</a>
            </div>
        </section>
    </div>

    <section class="page-section" aria-label="Additional information">
        <h2>More information</h2>
        <div class="page-list">
            <div class="page-list-item">
                <span><strong>View our estimated wait time and more!</strong><small>See current training information before joining!</small></span>
                <a class="btn btn-sm btn-primary" href="{{ route('trainingtimes') }}">Click HERE to learn more!</a>
            </div>
            <div class="page-list-item">
                <span><strong>Questions?</strong><small>We're happy to help!</small></span>
                <a class="btn btn-sm btn-primary" href="{{ route('staff') }}">Contact our Chief Instructor!</a>
            </div>
        </div>
    </section>
</main>
@endsection
