@extends('layouts.master')
@section('title', 'Join - Winnipeg FIR')
@section('description', 'Join the Winnipeg FIR!')

@section('content')

    <div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">Welcome to Winnipeg.</h1>
        <p>We're home to VATSIM's most modern training program, some of the best instructors around and a great controlling environment. Interested in joining? We're happy to help out.</p>
		
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active font-weight-bold" id="new-tab" data-toggle="tab" href="#new" role="tab" aria-controls="new" aria-selected="false">If You're New to VATSIM</a>
			</li>
			<li class="nav-item">
				<a class="nav-link font-weight-bold" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit" aria-selected="false">If You Have a Home FIR/ARTCC/vACC</a>
			</li>
		</ul>
		
	<div class="card">
		<div class="card-body">	
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new">
					<br>
					<h3 class="font-weight-bold blue-text">Step 1 – Join VATSIM</h3>
					<ol>
						<li>If you are currently not a member of VATSIM then you can sign up for an account at the<span> </span><a href="http://cert.vatsim.net/vatsimnet/signup.html" target="_blank" rel="noopener noreferrer">VATSIM Registration Page</a>. Ensure that you read all of the pages and instructions carefully.</li>
						<li>Under the preferred region field select the<span> </span><strong>Americas</strong><span> </span>region and the <strong>Canada</strong> division.</li>
						<li>After you complete your registration you receive an ID and password from VATSIM via email. When you have these two items, continue to step two.</li>
					</ol>
					<br>
					<h3 class="font-weight-bold blue-text">Step 2 – Join VATCAN &amp; Complete your S1 Exam</h3>
					<ol>
						<li>You will need to join the VATCAN division. If you have not already joined, or skipped some steps from Step 1, click the following link to set your region:<span> </span><a href="https://cert.vatsim.net/vatsimnet/regch.php">https://cert.vatsim.net/vatsimnet/regch.php</a>. You may proceed to step 5 if you are already a part of VATCAN.</li>
						<li>You will now get your choice to select a region and division to join. To join VATCAN select<span> </span><strong>Americas</strong><span> </span>as your region.</li>
						<li>In the select a division drop down box, select<span> </span><strong>Canada</strong>.</li>
						<li>Press<span> </span><strong>Continue</strong><span> </span>to set your selection.</li>
						<li>Follow the steps on <a href="https://vatcan.ca/How-to-Become-a-Controller" target="_blank" rel="noopener noreferrer">https://vatcan.ca/How-to-Become-a-Controller</a> (if you already have your S1 (or higher) rating, you may skip step 2 on this page.)</li>
					</ol>
					<br>
					<h3 class="font-weight-bold blue-text">Step 3 – Join the Winnipeg FIR</h3>
					<ol>
						<li><a href="http://vatcan.ca/login">Login into VATCAN</a> using your VATSIM CID and password.</li>
						<li>Click on <strong>My</strong> <strong>VATCAN</strong>, then on <strong>Transfer Request</strong>.</li>
						<li>In the <strong>New FIR</strong> drop-down, select <strong>Winnipeg FIR</strong>.</li>
						<li>Fill in your reasoning, and hit <strong>Submit</strong>.</li>
						<li>Your transfer will be reviewed by our FIR staff within 1-2 weeks.</li>
					</ol>
				</div>

				<div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit"><br>
					<div class="card">	
						<div class="card-body bg-warning">
							<text class="font-weight-bold">NOTE:</text> Winnipeg is currently busy with a home controller wait list - this may result in lengthy delays for visiting controllers awaiting training. Controllers will be contacted with updates when available. Thank you for your patience.</text>
						</div>
					</div>
					<br>
					<p>Winnipeg is always looking for controllers to visit our FIR. Whether it's for a change of scenery, to learn a new way of controlling, or just for fun, come visit Winnipeg!</p>
					<p>Members who are interested in visiting are now asked to visit the VATCAN.ca website to apply for visiting status.</p>
					<a class="ml-0 btn btn-success" href="https://vatcan.ca/my/visit">Apply Now!</a>
					</div>
				</div>
			</div>
		</div>

<br>
<hr>
	<h5 class="font-weight-bold blue-text">View our estimated wait time and more.</h5>
	<div class="row" style="padding-left:8px">
		<a class="btn btn-sm btn-primary" href="{{route('training')}}">Click HERE to learn more!</a>
	</div>

<hr>
<p>Questions? <a href="{{route('staff')}}">Contact our Chief Instructor!</a></p>
<br>

</div>
@endsection
