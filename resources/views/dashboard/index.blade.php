@extends('layouts.master')

@push('styles')
    <link href="{{ asset('css/pages/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')

@include('partials.vendor.dashboard-tour')
@section('title', 'Dashboard - Vancouver FIR')


<div class="dashboard-page">
    <div class="container dashboard-shell py-4">
        <header class="dashboard-header" data-step="1" data-intro="Welcome to your Dashboard! This is your central hub for all things Vancouver. Here you can interact with our FIR, and manage your account!">
            <div class="dashboard-header-copy">
                <span class="dashboard-header-label">Dashboard</span>
                <h1 class="fw-bold">Welcome to Vancouver, {{ auth()->user()->display_fname }}!</h1>
            </div>
            <a class="dashboard-tour-button" href="javascript:void(0);" onclick="startDashboardTour();">
                <i class="fas fa-compass me-1"></i>Tour
            </a>
        </header>

        <div class="dashboard-panel">
        <div class="row g-0 align-items-stretch dashboard-overview">
            <div class="col-12 {{ $profile ? 'col-xl-7' : '' }}">
                <div data-step="2" data-intro="Here is where you manage and view the data we store on you and your Vancouver FIR Profile" class="card">
                    <div class="card-body pb-0">
                        <div class="row g-3 align-items-center dashboard-identity">
                            <div data-step="5" data-intro="You can change your avatar here! Your avatar is available when people view your account! This will likely only be staff members, unless you sign up for an event or similar activity!" class="col-auto dashboard-avatar-control">
                                <div class="text-center">
                                    <img class="dashboard-avatar rounded-circle mb-2" src="{{ auth()->user()->avatar() }}" alt="{{ auth()->user()->fullName('FLC') }}">
                                    <div>
                                        <a role="button" data-bs-toggle="modal" data-bs-target="#changeAvatar" class="btn btn-sm btn-primary" href="#">Edit photo</a>
                                    </div>
                                    @if (!auth()->user()->isAvatarDefault())
                                        <form method="POST" action="{{route('users.resetavatar')}}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-link text-danger mt-1">Reset</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="col" data-step="3" data-intro="Here is an overview of your profile, including your CZVR roles! You can change the way your name is displayed by clicking on the 'Change display name' button [CoC A4(b)]!">
                                <h3 class="h4 fw-bold mb-1">{{ auth()->user()->fullName('FLC') }}</h3>
                                <div class="dashboard-meta">
                                    <span>{{ auth()->user()->rating_GRP }} ({{ auth()->user()->rating_short }})</span>
                                    <span>{{ auth()->user()->accessLabel() }}</span>
                                    @if (auth()->user()->staffProfile)
                                        <span>{{ auth()->user()->staffProfile->position }}</span>
                                    @endif
                                </div>
                                <a class="small fw-bold" data-bs-toggle="modal" data-bs-target="#ratingChange" href="#">
                                    Rating incorrect?
                                </a>
                            </div>
                        </div>
                        <details class="dashboard-account-settings">
                            <summary>
                            <span>
                                Account settings
                            </span>
                            </summary>
                        <ul class="list-unstyled mb-0 mt-2">
                            <li class="mb-1">
                                <button type="button" class="accordion" aria-expanded="false" aria-controls="discordPanel" data-step="4" data-intro="Connect or disconnect your Discord account here.">Discord connection</button>
                                <div class="panel" id="discordPanel">
                                    <div class="card-body">
                                        @if (!auth()->user()->hasDiscord())
                                            <p class="small text-muted mb-2"><i class="fa fa-circle-xmark text-danger me-1"></i> No Discord account linked</p>
                                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#discordModal">Link Discord</a>
                                        @else
                                            <p class="small mb-2"><i class="fa fa-check-circle text-success me-1"></i><img style="border-radius:50%; height: 24px;" class="img-fluid me-1" src="{{ auth()->user()->getDiscordAvatar() }}" alt="">{{ auth()->user()->getDiscordUser()->username }}</p>
                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#discordModal">Unlink</a>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" class="accordion" aria-expanded="false" aria-controls="displayNamePanel">Change Display Name</button>
                                <div class="panel" id="displayNamePanel">
                                    <form method="POST" action="{{ route('users.changedisplayname') }}">
                                        <div class="card-body">
                                            @csrf
                                            <div class="form-group">
                                                <h5 class="fw-bold blue-text">First Name</h5>
                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->display_fname }}" name="display_fname"
                                                    id="input_display_fname">
                                                <script>
                                                    function resetToCertFirstName() {
                                                        $("#input_display_fname").val("{{ auth()->user()->fname }}")
                                                    }
                                                </script>
                                            </div>
                                            <div class="form-group mt-2">
                                                <h5 class="fw-bold blue-text">Display Options</h5>
                                                <select name="format" class="form-select">
                                                    <option value="showall"> Show first name, last name, and CID (e.g. {{ auth()->user()->display_fname }} {{ auth()->user()->lname }} {{ auth()->id() }}) </option>
                                                    <option value="showfirstcid"> Show first name and CID (e.g. {{ auth()->user()->display_fname }} {{ auth()->id() }}) </option>
                                                    <option value="showcid"> Show CID only (e.g. {{ auth()->id() }}) </option>
                                                </select>
                                                <input type="submit" class="btn btn-sm btn-success ms-0 mt-2" value="Save">
                                                <a class="btn btn-sm btn-primary mt-2" role="button" onclick="resetToCertFirstName()">
                                                    <span> Reset to CERT first name </span>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <button type="button" class="accordion" aria-expanded="false" aria-controls="biographyPanel">Biography Editor</button>
                                <div class="panel" id="biographyPanel">
                                    <div class="card-body">
                                        <h5 class="fw-bold blue-text">Your Biography</h5>
                                        <form method="post" action="{{ route('me.editbio') }}">
                                            @csrf
                                            <textarea name="bio" style="height: 100%;" class="form-control">{{ auth()->user()->bio }}</textarea>
                                            <p class="mt-2 mb-2">Please ensure this complies with the VATSIM Code of Conduct.</p>
                                            <input type="submit" class="btn btn-sm btn-success ms-0" value="Save">
                                        </form>
                                    </div>
                                </div>
                                <button type="button" class="accordion" aria-expanded="false" aria-controls="preferencesPanel">Your Preferences</button>
                                <div class="panel" id="preferencesPanel">
                                    <div class="card-body">
                                        <h5 class="fw-bold blue-text">Current Subscription Status</h5>
                                        @if (auth()->user()->gdpr_subscribed_emails == 0)
                                            <h3><span class="badge text-bg-danger p-2">Not subscribed</span></h3>
                                        @else
                                            <h3><span class="badge text-bg-success p-2">Subscribed</span></h3>
                                        @endif
                                        <hr>
                                        <h5 class="fw-bold">What Does This Mean?</h5>
                                        <p> When you subscribe to our email service, you allow the Vancouver FIR to send you 'promotional' emails as defined by the European Union GDPR. These emails are typically not necessary to your continued participation in the FIR or holding an account with us on our system.<br /> Some examples would include:
                                        </p>
                                        <ul style="list-style: square">
                                            <li>Controller certifications for the quarter</li>
                                            <li>News from the FIR Chief about non-critical matters</li>
                                            <li>Updates from other staff members</li>
                                            <li>Event notifications</li>
                                        </ul>
                                        <p>To learn more about how we manage your data, please read our <a href="{{ url('/privacy') }}">privacy policy!</a></p>
                                        @if (auth()->user()->gdpr_subscribed_emails == 0)
                                            <form method="POST" action="{{ url('/dashboard/emailpref/subscribe') }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success ms-0">Subscribe to Vancouver Emails Now!</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ url('/dashboard/emailpref/unsubscribe') }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger ms-0">Unsubscribe</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        </ul>
                        </details>
                    </div>
                </div>
            </div>
            <!-- Vancouver Visitor/Exec/Home Hours -->
            @if ($profile)
            <div class="col-12 col-xl-5">
                <div class="card" data-step="7" data-intro="Here you can view your certification status within CZVR!">
                    <div class="card-body">
                        <h2 class="h3 fw-bold dashboard-card-title">Controller status</h2>

                        <div class="dashboard-statuses">
                            <span class="badge text-bg-{{ $status[0] }} rounded shadow-none"
                                @if(isset($status[3])) style="background-color: {{ $status[3] }}" @endif>
                                <i class="fa {{ $status[1] }}"></i>&nbsp;{{ $status[2] }}
                            </span>
                            <span class="badge text-bg-{{ $activeStatus[0] }} rounded shadow-none">
                                <i class="fa {{ $activeStatus[1] }}"></i>&nbsp;{{ $activeStatus[2] }}
                            </span>
                        </div>

                        <div class="text-danger">
                            @if ($certification === 'not_certified')
                                <p class="small mb-1">You are currently not certified to control. If you think this is a mistake, please contact us.</p>
                            @endif
                            @if ($active === 0)
                                <p class="small mb-1">You are inactive. Please contact the FIR Chief and do not control on the network.</p>
                            @endif
                        </div>

                        <!-- Activity -->
                        @if ($profile && $profile->status !== 'not_certified')
                            <div class="dashboard-activity">
                            <h3>Quarterly activity</h3>

                            @if ($profile->currency < 0.1)
                                <div><span class="badge red rounded shadow-none">No hours recorded</span></div>
                            @else
                                <div>
                                    <span class="badge {{ $profile->currency >= $requiredHours ? 'green' : 'purple' }} rounded shadow-none">
                                        {{ decimal_to_hm($profile->currency) }} recorded
                                    </span>
                                </div>
                            @endif

                            <p>You require <b>{{ $requiredHours }} hours</b> of activity every quarter!</p>
                            </div>
                        @endif

                        @if ($certification === 'not_certified')
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="{{ route('join.public') }}" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                        <span class="text-colour">How to Become a Vancouver Controller</span>
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            <div class="dashboard-support-bar" data-step="6" data-intro="Open and review your support tickets here.">
                <span>
                    <strong>Support</strong>
                    <small>{{ count($openTickets) }} open {{ count($openTickets) === 1 ? 'ticket' : 'tickets' }}</small>
                </span>
                <span class="dashboard-support-actions">
                    <a href="{{ route('tickets.index') }}">My tickets</a>
                    @can('tickets.manage')
                        <a href="{{ route('tickets.staff') }}">Staff tickets</a>
                    @endcan
                    <a href="{{ route('feedback.create') }}">Feedback</a>
                </span>
            </div>
        </div>
        <div class="dashboard-workspace">
            <div class="card" data-step="8" data-intro="Here you can view upcoming events and your signups for them!">
                <div class="card-body {{ count($confirmedevent) < 1 ? 'dashboard-inline-section' : '' }}">
                <h2 class="h3 fw-bold dashboard-card-title">Upcoming events</h2>
                <div>
                    @if (count($confirmedevent) < 1)
                        <div class="dashboard-event-empty">
                            <span>No upcoming events</span>
                            @if (count($unconfirmedapp) < 1)
                                <span>No active applications</span>
                            @elseif (count($unconfirmedapp) == 1)
                                <a href="#" data-bs-target="#unconfirmedEvents" data-bs-toggle="modal">1 active application</a>
                            @else
                                <a href="#" data-bs-target="#unconfirmedEvents" data-bs-toggle="modal">{{ count($unconfirmedapp) }} active applications</a>
                            @endif
                        </div>
                    @else
                        @foreach ($confirmedevent as $cevent)
                            <div class="dashboard-event">
                                <div class="dashboard-event-date">
                                    {{ $cevent->start_timestamp->format('d') }}
                                    <small>{{ $cevent->start_timestamp->format('M') }}</small>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="{{ route('events.view', $cevent->slug) }}" class="fw-bold">{{ $cevent->name }}</a>
                                    <div class="small text-muted mt-1">{{ $cevent->start_timestamp_pretty() }}</div>
                                    @foreach ($confirmedapp as $capp)
                                        @if ($cevent->id == $capp->event->id)
                                            <div class="small mt-2">
                                                <span class="badge text-bg-success me-1">Confirmed</span>
                                                {{ $capp->airport }} · {{ ($capp->start_timestamp)->format('H:i') }}z–{{ ($capp->end_timestamp)->format('H:i') }}z
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mt-3">
                            @if (count($unconfirmedapp) < 1)
                                <span class="text-muted">No active applications</span>
                            @elseif (count($unconfirmedapp) == 1)
                                <a href="#" data-bs-target="#unconfirmedEvents" data-bs-toggle="modal">1 active application</a>
                            @else
                                <a href="#" data-bs-target="#unconfirmedEvents" data-bs-toggle="modal">{{ count($unconfirmedapp) }} active applications</a>
                            @endif
                            <a href="{{ url('/dashboard/events/view') }}" class="btn btn-sm btn-outline-primary">View event rosters</a>
                        </div>
                    @endif
                </div>
                </div>
            </div>
        @if (auth()->user()->hasAnyPermission(['roster.manage', 'news.manage', 'feedback.manage', 'users.manage', 'training.administer', 'roles.manage', 'site-settings.manage', 'staff.manage', 'audit-log.access', 'network.manage']))
            <details class="dashboard-management-disclosure">
                <summary>
                    <span>
                        Management
                    </span>
                </summary>
            <section class="dashboard-management">
                @can('roster.manage')
                    <a class="dashboard-tool" href="{{ route('roster.public') }}">Controller roster</a>
                @endcan
                @can('news.manage')
                    <a class="dashboard-tool" href="{{ route('news.index') }}">News</a>
                @endcan
                @can('feedback.manage')
                    <a class="dashboard-tool" href="{{ route('staff.feedback.index') }}">Feedback</a>
                @endcan
                @can('users.manage')
                    <a class="dashboard-tool" href="{{ route('users.viewall') }}">Users</a>
                @endcan
                @can('training.administer')
                    <a class="dashboard-tool" href="{{ route('trainingtimes') }}">Training Time</a>
                @endcan
                @can('settings.access')
                    <a class="dashboard-tool" href="{{ route('settings.index') }}">Settings</a>
                @endcan
                @can('network.manage')
                    <a class="dashboard-tool" href="{{ route('network.index') }}">Network data</a>
                @endcan
            </section>
            </details>
        @endif
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
            var isOpen = Boolean(panel.style.maxHeight);
            this.setAttribute("aria-expanded", isOpen ? "false" : "true");
            if (isOpen) {
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="discord-avatar-form" method="POST" action="{{ route('users.changeavatar.discord') }}" class="d-none">
                @csrf
                @method('PATCH')
            </form>
            <form method="post" action="{{ route('users.changeavatar') }}" enctype="multipart/form-data" class="" id="">
                <div class="modal-body">
                    <p>Please ensure your avatar complies with the VATSIM Code of Conduct. This avatar will be visible to staff members, if you place a controller booking, and if you're a staff member yourself, on the staff page.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="file">

                        </div>
                    </div>
                    @if (auth()->user()->hasDiscord())
                        or use your Discord avatar (refreshes every 6 hours)<br />
                        <p class="mt-1"><img style="border-radius:50%; height: 60px;" class="img-fluid" src="{{ auth()->user()->getDiscordAvatar() }}" alt="">
                            <button type="submit" form="discord-avatar-form" class="btn btn-outline-success bg-CZQO-blue-light mt-3">Use Discord Avatar</button>
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
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
                <form id="rating-logout-form" method="POST" action="{{route('auth.logout')}}" class="d-none">@csrf</form>
                <p>If you would like to update your rating on our website, you may <button type="submit" form="rating-logout-form" class="border-0 bg-transparent p-0 blue-text">logout</button> and log back in, or wait until we update it automatically!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-light" data-bs-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>
<!-- End Rating Change modal -->

<!-- Discord Link/Join Modal -->
<div class="modal fade" id="discordModal" tabindex="-1" role="dialog" aria-labelledby="discordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            @if (!auth()->user()->hasDiscord())
                <div class="modal-header">
                    <h5 class="modal-title" id="discordModalTitle">Join the Vancouver FIR Discord</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            @else
                <div class="modal-header">
                    <h5 class="modal-title">Unlink your Discord account</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <p>Unlinking will remove you from the Discord server, remove your avatar, and stop sending you notifications on events and training!</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                    <form method="POST" action="{{ route('me.discord.unlink') }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Unlink Account</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- End Discord Modal -->

<!--Unconfirmed events modal-->
<div class="modal fade" id="unconfirmedEvents" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Events You've Applied For</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($confirmedevent as $cevent)
                    <h5><span class="fw-bold blue-text">{{ $cevent->name }}</span> on
                        {{ $cevent->start_timestamp_pretty() }}</h5>
                    @foreach ($unconfirmedapp as $uapp)
                        @if ($cevent->name == $uapp->event->name)
                            <li>
                                <span class="fw-bold"> Position Requested:</span> {{ $uapp->position }} from {{ $uapp->start_availability_timestamp }}z - {{ $uapp->end_availability_timestamp }}z
                            </li> <br />
                        @endif
                    @endforeach
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>
<!--End unconfirmed events modal-->

@endsection
