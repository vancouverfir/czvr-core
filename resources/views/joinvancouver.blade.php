@extends('layouts.master')
@section('title', 'Join - Vancouver FIR')
@section('description', 'Join the Vancouver FIR!')

@push('styles')
<style>
.join-page {
    margin: 0 auto;
    max-width: 1040px;
    padding: 3rem 1rem 4rem;
}
.join-intro {
    border-bottom: 1px solid var(--czvr-border);
    margin-bottom: 1.75rem;
    padding-bottom: 1.75rem;
}
.join-eyebrow {
    color: var(--czvr-green-dark);
    display: block;
    font-size: var(--czvr-font-size-base);
    letter-spacing: .1em;
    margin-bottom: .55rem;
    text-transform: uppercase;
}
.join-intro h1 {
    font-size: clamp(2.25rem, 4vw, 3.5rem);
    font-weight: 450;
    letter-spacing: -.045em;
    margin: 0 0 .7rem;
}
.join-intro p {
    color: var(--czvr-text-muted);
    line-height: 1.65;
    margin: 0;
    max-width: 760px;
}
.join-path-label {
    color: var(--czvr-text-muted);
    font-size: var(--czvr-font-size-base);
    margin: 0 0 .55rem;
}
.join-paths {
    background: var(--czvr-surface-muted);
    border: 1px solid var(--czvr-border);
    border-radius: .65rem;
    display: grid;
    gap: .35rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-bottom: 2rem;
    padding: .35rem;
}
.join-paths .nav-link {
    background: transparent;
    border: 0;
    border-radius: .45rem;
    color: var(--czvr-text-muted);
    font-size: var(--czvr-font-size-base);
    padding: .75rem 1rem;
    text-align: center;
}
.join-paths .nav-link:hover,
.join-paths .nav-link:focus {
    color: var(--czvr-green-dark);
}
.join-paths .nav-link.active {
    background: #fff;
    box-shadow: 0 2px 8px rgba(28, 49, 31, .08);
    color: var(--czvr-green-dark);
}
.join-step-list {
    display: grid;
    gap: .9rem;
}
.join-step {
    border: 1px solid var(--czvr-border);
    border-radius: .7rem;
    display: grid;
    grid-template-columns: 70px minmax(0, 1fr);
    overflow: hidden;
}
.join-step-number {
    align-items: flex-start;
    background: var(--czvr-green-soft);
    color: var(--czvr-green-dark);
    display: flex;
    font-size: 1.3rem;
    font-weight: 500;
    justify-content: center;
    padding-top: 1.25rem;
}
.join-step-content {
    padding: 1.15rem 1.3rem;
}
.join-step-content h2 {
    color: var(--czvr-text);
    font-size: 1rem;
    font-weight: 500;
    margin: 0;
}
.join-step-summary {
    color: var(--czvr-text-muted);
    font-size: var(--czvr-font-size-base);
    margin: .3rem 0 0;
}
.join-details {
    border-top: 1px solid var(--czvr-border);
    margin-top: .85rem;
    padding-top: .75rem;
}
.join-details summary {
    color: var(--czvr-green-dark);
    cursor: pointer;
    font-size: var(--czvr-font-size-base);
    list-style: none;
    width: max-content;
}
.join-details summary::-webkit-details-marker { display: none; }
.join-details summary::after {
    content: '+';
    display: inline-block;
    margin-left: .45rem;
}
.join-details[open] summary::after { content: '−'; }
.join-details ol {
    color: var(--czvr-text);
    line-height: 1.65;
    margin: .85rem 0 .15rem;
    padding-left: 1.3rem;
}
.join-details li {
    margin-bottom: .65rem;
    padding-left: .25rem;
}
.join-page a:not(.btn):not(.nav-link) {
    text-decoration: underline;
    text-decoration-color: rgba(40, 94, 27, .4);
    text-underline-offset: .18rem;
}
.join-page a:not(.btn):not(.nav-link):hover {
    text-decoration-color: var(--czvr-green-dark);
}
.join-visitor {
    border: 1px solid var(--czvr-border);
    border-radius: .75rem;
    overflow: hidden;
}
.join-visitor-note {
    background: #33B5E5;
    border-bottom: 1px solid #eadcb4;
    color: #ffffff;
    line-height: 1.55;
    margin: 0;
    padding: 1rem 1.2rem;
}
.join-visitor-note strong {
    display: block;
    font-size: var(--czvr-font-size-base);
    letter-spacing: .08em;
    margin-bottom: .25rem;
    text-transform: uppercase;
}
.join-visitor-body {
    padding: 1.35rem;
}
.join-visitor-body p {
    line-height: 1.65;
    margin-bottom: .8rem;
}
.join-visitor-body .btn {
    margin-top: .25rem;
}
.join-next {
    border-top: 1px solid var(--czvr-border);
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-top: 2.5rem;
    padding-top: 1.5rem;
}
.join-next-item {
    align-items: center;
    display: flex;
    gap: 1rem;
    justify-content: space-between;
}
.join-next-item + .join-next-item {
    border-left: 1px solid var(--czvr-border);
    padding-left: 1.25rem;
}
.join-next h2 {
    font-size: var(--czvr-font-size-base);
    font-weight: 500;
    margin: 0 0 .2rem;
}
.join-next p {
    color: var(--czvr-text-muted);
    font-size: var(--czvr-font-size-base);
    margin: 0;
}
.join-next .btn {
    flex: 0 0 auto;
    white-space: nowrap;
}
@media (max-width: 767.98px) {
    .join-page { padding: 2rem 1rem 3rem; }
    .join-paths { grid-template-columns: 1fr; }
    .join-step { grid-template-columns: 50px minmax(0, 1fr); }
    .join-step-number { font-size: 1rem; }
    .join-next { grid-template-columns: 1fr; }
    .join-next-item { align-items: flex-start; flex-direction: column; }
    .join-next-item + .join-next-item { border-left: 0; border-top: 1px solid var(--czvr-border); padding: 1.25rem 0 0; }
}
</style>
@endpush

@section('content')
<main class="join-page">
    <header class="join-intro">
        <h1 class="blue-text">Welcome to Vancouver!</h1>
        <p>We're home to VATSIM's most modern training program, some of the best instructors around and a great controlling environment! Interested in joining? We're happy to help out!</p>
    </header>

    <nav class="nav join-paths" role="tablist" aria-label="Controller application path">
        <button class="nav-link active" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab" aria-controls="new" aria-selected="true">If You're New to VATSIM</button>
        <button class="nav-link" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit" type="button" role="tab" aria-controls="visit" aria-selected="false">If You Have a Home FIR/ARTCC/vACC</button>
    </nav>

    <div class="tab-content">
        <section class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new-tab" tabindex="0">
            <div class="join-step-list">
                <article class="join-step">
                    <div class="join-step-number" aria-hidden="true">01</div>
                    <div class="join-step-content">
                        <h2>Step 1 – Join VATSIM</h2>
                        <p class="join-step-summary">Create your account and select Canada as your division.</p>
                        <details class="join-details" open>
                            <summary>View instructions</summary>
                            <ol>
                                <li>If you are currently not a member of VATSIM then you can sign up for an account at the <a href="http://cert.vatsim.net/vatsimnet/signup.html" target="_blank" rel="noopener noreferrer">VATSIM Registration Page</a>. Ensure that you read all of the pages and instructions carefully.</li>
                                <li>Under the preferred region field select the <strong>Americas</strong> region and the <strong>Canada</strong> division.</li>
                                <li>After you complete your registration you receive an ID and password from VATSIM via email. When you have these two items, continue to step two.</li>
                            </ol>
                        </details>
                    </div>
                </article>

                <article class="join-step">
                    <div class="join-step-number" aria-hidden="true">02</div>
                    <div class="join-step-content">
                        <h2>Step 2 – Join VATCAN &amp; Complete your S1 Exam</h2>
                        <p class="join-step-summary">Confirm your region and complete VATCAN's controller onboarding.</p>
                        <details class="join-details">
                            <summary>View instructions</summary>
                            <ol>
                                <li>You will need to join the VATCAN division. If you have not already joined, or skipped some steps from Step 1, click the following link to set your region: <a href="https://cert.vatsim.net/vatsimnet/regch.php" target="_blank" rel="noopener noreferrer">https://cert.vatsim.net/vatsimnet/regch.php</a>. You may proceed to step 5 if you are already a part of VATCAN.</li>
                                <li>You will now get your choice to select a region and division to join. To join VATCAN select <strong>Americas</strong> as your region.</li>
                                <li>In the select a division drop down box, select <strong>Canada</strong>.</li>
                                <li>Press <strong>Continue</strong> to set your selection.</li>
                                <li>Follow the steps on <a href="https://vatcan.ca/How-to-Become-a-Controller" target="_blank" rel="noopener noreferrer">https://vatcan.ca/How-to-Become-a-Controller</a> (if you already have your S1 (or higher) rating, you may skip step 3 on this page.)</li>
                            </ol>
                        </details>
                    </div>
                </article>

                <article class="join-step">
                    <div class="join-step-number" aria-hidden="true">03</div>
                    <div class="join-step-content">
                        <h2>Step 3 – Join the Vancouver FIR</h2>
                        <p class="join-step-summary">Submit your transfer request to Vancouver through VATCAN.</p>
                        <details class="join-details">
                            <summary>View instructions</summary>
                            <ol>
                                <li><a href="https://vatcan.ca/login" target="_blank" rel="noopener noreferrer">Login into VATCAN</a> using your VATSIM CID and password.</li>
                                <li>Click on <strong>My VATCAN</strong>, then on <strong>Transfer Request</strong>.</li>
                                <li>In the <strong>New FIR</strong> drop-down, select <strong>Vancouver FIR</strong>.</li>
                                <li>Fill in your reasoning, and hit <strong>Submit</strong>.</li>
                                <li>Your transfer will be reviewed by our FIR staff within 1-2 weeks.</li>
                            </ol>
                        </details>
                    </div>
                </article>
            </div>
        </section>

        <section class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab" tabindex="0">
            <div class="join-visitor">
                <p class="join-visitor-note"><strong>Note</strong>Vancouver is currently busy with a home controller wait list - this may result in lengthy delays for visiting controllers awaiting training! Controllers will be contacted with updates when available! Thank you for your patience!</p>
                <div class="join-visitor-body">
                    <p>Vancouver is always looking for controllers to visit our FIR! Whether it's for a change of scenery, to learn a new way of controlling, or just for fun, come visit Vancouver!</p>
                    <p>Members who are interested in visiting are now asked to visit the VATCAN.CA website to apply for visiting status!</p>
                    <a class="btn btn-success" href="https://vatcan.ca/my/visit" target="_blank" rel="noopener noreferrer">Apply Now!</a>
                </div>
            </div>
        </section>
    </div>

    <section class="join-next" aria-label="Additional information">
        <div class="join-next-item">
            <div>
                <h2>View our estimated wait time and more!</h2>
                <p>See current training information before joining!</p>
            </div>
            <a class="btn btn-sm btn-outline-success" href="{{ route('trainingtimes') }}">Click HERE to learn more!</a>
        </div>
        <div class="join-next-item">
            <div>
                <h2>Questions?</h2>
                <p>We're happy to help!</p>
            </div>
            <a class="btn btn-sm btn-outline-success" href="{{ route('staff') }}">Contact our Chief Instructor!</a>
        </div>
    </section>
</main>
@endsection
