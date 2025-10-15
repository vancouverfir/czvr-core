@extends('layouts.master')

@section('content')

@section('title', 'Dashboard - Vancouver FIR')

<style>
    .accordion {
    background-color: #191919;
    color: #fff;
    cursor: pointer;
    padding: 2%;
    width: 100%;
    border: none;
    text-align: left;
    outline: none !important;
    font-size: 12px;
    transition: 0.4s;
    }

    .accordion:hover {
    background-color: #2E2F2F;
    color: #fff;
    }

    .active {
    background-color: #444;
    color: #fff;
    }

    .accordion:after {
    font-family: "Font Awesome 5 Free";
    content: '\f104';
    float: right;
    font-weight: 900;
    }

    .active:after {
    font-family: "Font Awesome 5 Free";
    content: "\f107";
    font-weight: 900;
    }

    .panel {
    background-color: #2E2F2F;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.1s ease-out;
    }

    .introjs-tooltip {
    background: #2E2F2F !important;
    color: #fff !important;
    }

    .introjs-button {
    background: #007bff !important;
    color: #fff !important;
    box-shadow: none !important;
    text-shadow: none !important;
    filter: none !important;
    }

    .introjs-helperLayer,
    .introjs-overlay {
    background: rgba(0, 0, 0, 0.6) !important;
    box-shadow: 0 0 0 2px #007bff !important;
    }
</style>

<div style="background-color: #222">
    <div class="container py-4">
        <h1 data-step="1" data-intro="Welcome to your Dashboard! This is your central hub for all things Vancouver. Here you can interact with our FIR, and manage your account!" class="font-weight-bold white-text">Dashboard</h1>
        <a style="color: white" href="javascript:void(0);" onclick="javascript:introJs().setOption('showProgress', true).start();">Dashboard Tutorial</a>
        <br class="my-2">
        <div class="row">
            <div class="col">
                <div data-step="2" data-intro="Here is where you manage and view the data we store on you and your Vancouver FIR Profile!" class="card">
                    <div class="card-body pb-0">
                        <h3 class="font-weight-bold blue-text pb-2">Profile</h3>
                        <div class="row">
                            <div class="col" data-step="3" data-intro="Here is an overview of your profile, including your CZVR roles! You can change the way your name is displayed by clicking on the 'Change display name' button [CoC A4(b)]!">
                                <h5 class="font-weight-bold card-title"> {{ Auth::user()->fullName('FLC') }} </h5>
                                <h6 class="card-subtitle text-muted mb-2"> {{ Auth::user()->rating_GRP }} ({{ Auth::user()->rating_short }}) </h6>
                                <p><a class="font-italic" style="color: #fff;" data-toggle="modal" data-target="#ratingChange">Rating incorrect?</a></p>
                                Role: {{ Auth::user()->permissions() }} <br />
                                @if (Auth::user()->staffProfile)
                                    Staff Role: {{ Auth::user()->staffProfile->position }}
                                @endif
                                <hr>
                                <div data-step="4" data-intro="Here you can link your Discord account to receive reminders for training sessions, and gain access to the CZVR Discord!">
                                    <h5 class="font-weight-bold blue-text mt-2">Discord</h5>
                                    <hr>
                                    @if (!Auth::user()->hasDiscord())
                                        <p class="mt-1"><i class="fa fa-times-circle" style="color:red"></i> You haven't joined our Discord yet! </p>
                                        <a href="#" class="btn-sm btn-primary m-0 mt-1" data-toggle="modal" data-target="#discordModal"> Join Vancouver FIR Discord </a>
                                        <hr>
                                    @else
                                        <p class="mt-1"><i class="fa fa-check-circle" style="color:green"> </i> <img style="border-radius:50%; height: 30px;" class="img-fluid" src="{{ Auth::user()->getDiscordAvatar() }}" alt="">&nbsp;&nbsp;{{ Auth::user()->getDiscordUser()->username }}
                                        </p>
                                        <a href="#" class="btn-sm btn-danger m-0 mt-1" data-toggle="modal" data-target="#discordModal">Unlink</a>
                                        <hr>
                                    @endif
                                </div>
                            </div>

                            <div data-step="5" data-intro="You can change your avatar here! Your avatar is available when people view your account! This will likely only be staff members, unless you sign up for an event or similar activity!" class="col">
                                <h5 class="blue-text font-weight-bold card-title text-center" style="padding-bottom: 2%">Avatar</h5>
                                <div class="text-center">
                                    <img src="{{ Auth::user()->avatar() }}" style="width: 125px; height: 125px; margin-bottom: 10px; border-radius: 50%;">
                                </div>

                                <center><a role="button" data-toggle="modal" data-target="#changeAvatar" class="btn btn-sm btn-primary" href="#">Change</a></center>
                                @if (!Auth::user()->isAvatarDefault())
                                    <center><a role="button" class="btn btn-sm btn-danger" href="{{route('users.resetavatar')}}">Reset</a></center>
                                @endif
                            </div>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-1">
                                &nbsp;
                                <button class="accordion">Change Display Name</button>
                                <div class="panel">
                                    <form method="POST" action="{{ route('users.changedisplayname') }}">
                                        <div class="card-body pb-0">
                                            @csrf
                                            <div class="form-group">
                                                <h5 class="font-weight-bold blue-text">First Name</h5>
                                                <input type="text" class="form-control"
                                                    value="{{ Auth::user()->display_fname }}" name="display_fname"
                                                    id="input_display_fname">
                                                <br>
                                                <script>
                                                    function resetToCertFirstName() {
                                                        $("#input_display_fname").val("{{ Auth::user()->fname }}")
                                                    }
                                                </script>
                                            </div>
                                            <div class="form-group">
                                                <h5 class="font-weight-bold blue-text">Display Options</h5>
                                                <select name="format" class="custom-select">
                                                    <option value="showall"> Show first name, last name, and CID (e.g. {{ Auth::user()->display_fname }} {{ Auth::user()->lname }} {{ Auth::id() }}) </option>
                                                    <option value="showfirstcid"> Show first name and CID (e.g. {{ Auth::user()->display_fname }} {{ Auth::id() }}) </option>
                                                    <option value="showcid"> Show CID only (e.g. {{ Auth::id() }}) </option>
                                                </select>
                                                <br>
                                                <input type="submit" class="btn btn-sm btn-success ml-0 mt-4" value="Save">
                                                <a class="btn btn-sm btn-primary mt-4" role="button" onclick="resetToCertFirstName()">
                                                    <span> Reset to CERT first name </span>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <button class="accordion">Biography Editor</button>
                                <div class="panel">
                                    <div class="card-body pb-0">
                                        <h5 class="font-weight-bold blue-text">Your Biography</h5>
                                        <form method="post" action="{{ route('me.editbio') }}">
                                            @csrf
                                            <textarea name="bio" style="height: 100%;" class="form-control">{{ Auth::user()->bio }}</textarea> <br>
                                            <p>Please ensure this complies with the VATSIM Code of Conduct.</p>
                                            <input type="submit" class="btn btn-sm btn-success ml-0" value="Save">
                                        </form><br>
                                    </div>
                                </div>
                                <button class="accordion">Your Preferences</button>
                                <div class="panel">
                                    <div class="card-body pb-0">
                                        <h5 class="font-weight-bold blue-text">Current Subscription Status</h5>
                                        @if (Auth::user()->gdpr_subscribed_emails == 0)
                                            <h3><span class="badge badge-danger p-2">Not subscribed</span></h3>
                                        @else
                                            <h3><span class="badge badge-success p-2">Subscribed</span></h3>
                                        @endif
                                        <hr>
                                        <h5 class="font-weight-bold">What Does This Mean?</h5>
                                        <p> When you subscribe to our email service, you allow the Vancouver FIR to send you 'promotional' emails as defined by the European Union GDPR. These emails are typically not necessary to your continued participation in the FIR or holding an account with us on our system.<br /> Some examples would include:
                                        </p>
                                        <ul style="list-style: square">
                                            <li>Controller certifications for the quarter</li>
                                            <li>News from the FIR Chief about non-critical matters</li>
                                            <li>Updates from other staff members</li>
                                            <li>Event notifications</li>
                                        </ul>
                                        <br>
                                        <p>To learn more about how we manage your data, please read our <a href="{{ url('/privacy') }}">privacy policy!</a></p>
                                        <br>
                                        @if (Auth::user()->gdpr_subscribed_emails == 0)
                                            <a role="button" class="btn btn-sm btn-success ml-0 mt-3" href="{{ url('/dashboard/emailpref/subscribe') }}">Subscribe to Vancouver Emails Now!</a>
                                        @else
                                            <a role="button" class="btn btn-sm btn-danger ml-0 mt-3" href="{{ url('/dashboard/emailpref/unsubscribe') }}">Unsubscribe</a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <br />
            <div class="col">
                <div class="card">
                    <div class="card-body" data-step="6" data-intro="Here you can view upcoming Vancouver events!">
                        <h3 class="font-weight-bold blue-text pb-2">Upcoming Events</h3>
                        <div class="list-group">
                            @if (count($confirmedevent) < 1)
                                <h5>There are no scheduled events!</h5>
                            @else
                                @foreach ($confirmedevent as $cevent)
                                    <h5><b><a href="{{ route('events.view', $cevent->slug) }}" class="blue-text">{{ $cevent->name }}</a></b> on {{ $cevent->start_timestamp_pretty() }}</h5>
                                    <br>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <br />
                <div class="card" data-step="7" data-intro="If you have any enquires or issues for the staff, feel free to make a ticket via the ticketing system!">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Support</h3>
                        <ul class="list-unstyled mb-0 mt-2">
                            <h5 class="font-weight-bold">Tickets</h5>
                            @if (count($openTickets) < 1)
                                You have no open support tickets
                                <br /><br />
                            @else
                                <h5 style="font-weight: bold">
                                    @if (count($openTickets) == 1)
                                        1 open ticket
                                    @else
                                        {{ count($openTickets) }} open tickets
                                    @endif
                                </h5>
                                <div class="list-group">
                                    @foreach ($openTickets as $ticket)
                                        <a href="{{ url('/dashboard/tickets/' . $ticket->ticket_id) }}" class="list-group-item list-group-item-action rounded-0">{{ $ticket->title }} <br />
                                            <small title="{{ $ticket->updated_at }} (GMT+0, Zulu)">Last updated {{ $ticket->updated_at_pretty() }}</small>
                                        </a>
                                    @endforeach
                                </div>
                                <br>
                            @endif
                            <li class="mb-2">
                                <a href="{{ route('tickets.index') }}" style="text-decoration:none;">
                                    <span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span class="text-colour">Open a Support Ticket</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('feedback.create') }}" style="text-decoration:none;">
                                    <span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span class="text-colour">Send feedback</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('me.data') }}" style="text-decoration:none;">
                                    <span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span class="text-colour">Manage your data</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
</script>

<!--Change avatar modal-->
<div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Change avatar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('users.changeavatar') }}" enctype="multipart/form-data" class="" id="">
                <div class="modal-body">
                    <p>Please ensure your avatar complies with the VATSIM Code of Conduct. This avatar will be visible to staff members, if you place a controller booking, and if you're a staff member yourself, on the staff page.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file">
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    </div>
                    @if (Auth::user()->hasDiscord())
                        or use your Discord avatar (refreshes every 6 hours)<br />
                        <p class="mt-1"><img style="border-radius:50%; height: 60px;" class="img-fluid" src="{{ Auth::user()->getDiscordAvatar() }}" alt="">
                            <a href="{{ route('users.changeavatar.discord') }}" class="btn btn-outline-success bg-CZQO-blue-light mt-3">Use Discord Avatar</a>
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <input type="submit" class="btn btn-success" value="Upload">
                </div>
            </form>
        </div>
    </div>
</div>
<!--End change avatar modal-->

<!-- Start Rating Change modal -->
<div class="modal fade" id="ratingChange" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">How can I update my rating?</h5>
            </div>
            <div class="modal-body">
                <p>If you would like to update your rating on our website, you may <a href="/logout" class="blue-text">logout</a> and log back in, or wait until we update it automatically!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-light" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>
<!-- End Rating Change modal -->

<!-- Discord Link/Join Modal -->
<div class="modal fade" id="discordModal" tabindex="-1" role="dialog" aria-labelledby="discordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            @if (!Auth::user()->hasDiscord())
                <div class="modal-header">
                    <h5 class="modal-title" id="discordModalTitle">Join the Vancouver FIR Discord</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img src="{{ asset('/img/discord/czvrdiscord.png') }}" class="img-fluid mb-3" style="height:70px;" alt="CZVR Discord">
                    <p>Join the Vancouver FIR Discord to connect with fellow controllers and pilots and receive real-time updates on events and training!</p>
                    <p>We require your Discord authorization to add you in our server, information on data stored through Discord OAuth is available in our <a href="{{ route('privacy') }}" class="blue-text">Privacy Policy</a></p>
                </div>

                <div class="modal-footer justify-content-center">
                    <a href="{{ route('me.discord.link') }}" class="btn btn-primary">Join Discord</a>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            @else
                <div class="modal-header">
                    <h5 class="modal-title">Unlink your Discord account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <p>Unlinking will remove you from the Discord server, remove your avatar, and stop sending you notifications on events and training!</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a href="{{ route('me.discord.unlink') }}" class="btn btn-danger">Unlink Account</a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- End Discord Modal -->

@endsection
