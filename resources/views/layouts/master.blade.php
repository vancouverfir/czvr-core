<!doctype html>
<html lang="en">
    <head>
        @php
            $coreSettings ??= \App\Models\Settings\CoreSettings::query()->find(1);
            $isHoliday = now('UTC')->month === 12;
        @endphp
        <!--
        {{ $coreSettings?->sys_name ?? 'Vancouver FIR Core' }}
        {{ $coreSettings?->release ?? 'Development' }} ({{ $coreSettings?->sys_build ?? 'local' }})
        Built on Bootstrap 5 and Laravel 13

        Written by Liesel D... edited by a hundred Vancouverers

        For Flight Simulation Use Only - Not to be used for real-world navigation. All content on this web site may not be shared, copied, reproduced or used in any way without prior express written consent of Vancouver FIR. © Copyright {{ $coreSettings?->copyright_year ?? now()->year }} Vancouver FIR, All Rights Reserved.
        -->

        <!--Metadata-->

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @if ($isHoliday)
            <link rel="icon" href="{{ asset('holiday.ico') }}" type="image/x-icon">
        @else
            <link rel="icon" href="{{ asset('CZVR_Mark.svg') }}" type="image/svg+xml">
            <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">
        <title>@yield('title', 'Vancouver FIR')</title>
        <meta name="description" content="@yield('description', '')">
        <meta property="og:title" content="@yield('title', 'Vancouver FIR')">
        <meta property="og:description" content="@yield('description', '')">
        <meta property="og:image" content="@yield('image','https://cdn.discordapp.com/attachments/800588233570123776/1051930179821391912/Wordmark_Colour.png')">
        <!-- Package-managed vendor styles -->
        <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/fontawesome/all.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/fontawesome/v4-shims.min.css') }}" rel="stylesheet">

        @stack('vendor-styles')

        <!-- Shared application theme -->
        <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
        @stack('styles')

        <!-- Package-managed vendor scripts -->
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <!-- Libraries a page needs (partials/vendor); Select2 and Flatpickr are loaded by form-controls.js when a page has those fields -->
        @stack('vendor')
    </head>
    <body class="background">
    <!--Header-->
    @if ($isHoliday)
        <script src="{{ asset('js/snowflakes.js') }}"></script>
    @endif
    @if ($coreSettings?->banner)
        <div class="alert alert-{{ $coreSettings->bannerMode }}" style="margin: 0; border-radius: 0; border: none;">
            <div class="text-center align-self-center">
                @if(filled($coreSettings->bannerLink))
                    <a href="{{ $coreSettings->bannerLink }}" target="_blank" rel="noopener"><span style="margin: 0;">{{ $coreSettings->banner }}</span></a>
                @else
                    <span style="margin: 0;">{{ $coreSettings->banner }}</span>
                @endif
            </div>
        </div>
    @endif
    <header>
        <nav class="navbar navbar-expand-lg navbar-light p-0 site-navbar" style="min-height:59px">
            <div class="container">
                <a class="navbar-brand" href="{{route('index')}}"><img width="310" height="35" style="height: 35px; width:auto; vertical-align:inherit;" src="{{ asset($isHoliday ? 'Holiday Long.png' : 'CZVR_Colour_Long.svg') }}" alt="Vancouver FIR"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @can('news.manage')
                            <li class="nav-item dropdown {{ request()->routeIs('news', 'news.*') ? 'active' : '' }}">
                                <a class="nav-link dropdown-toggle" style="cursor:pointer" id="newsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">News</a>
                                <div class="dropdown-menu" aria-labelledby="newsDropdown">
                                    <a class="dropdown-item {{ request()->routeIs('news', 'news.articlepublic') ? 'active white-text' : '' }}" href="{{route('news')}}">News</a>
                                    <a class="dropdown-item {{ request()->routeIs('news.index', 'news.articles.*') ? 'active white-text' : '' }}" href="{{route('news.index')}}">Manage News</a>
                                </div>
                            </li>
                        @else
                            <li class="nav-item {{ request()->routeIs('news', 'news.*') ? 'active' : '' }}">
                                <a href="{{route('news')}}" class="nav-link">News</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown {{ request()->routeIs('events.*', 'editmap') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="eventsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Events</a>
                            <div class="dropdown-menu" aria-labelledby="eventsDropdown">
                                <a class="dropdown-item {{ request()->routeIs('events.index', 'events.view') ? 'active white-text' : '' }}" href="{{route('events.index')}}">Events</a>
                                <a class="dropdown-item" href="{{ route('events.index') }}#eventCoverage">Need ATC Coverage?</a>
                                @can('events.manage')
                                    <a class="dropdown-item {{ request()->routeIs('events.admin.*') ? 'active white-text' : '' }}" href="{{route('events.admin.index')}}">Manage Events</a>
                                    <a class="dropdown-item {{ request()->routeIs('editmap') ? 'active white-text' : '' }}" href="{{ route('editmap') }}">Edit Map</a>
                                @endcan
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('booking', 'booking.*', 'roster.public', 'roster.*', 'join.public', 'policies') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="atcDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ATC</a>
                            <div class="dropdown-menu" aria-labelledby="atcDropdown">
                            <a class="dropdown-item {{ request()->routeIs('booking', 'booking.*') ? 'active white-text' : '' }}" href="{{route('booking')}}">ATC Booking</a>
                            <a class="dropdown-item {{ request()->routeIs('roster.public') ? 'active white-text' : '' }}" href="{{route('roster.public')}}">Roster</a>
                            @if(!auth()->check() || ! auth()->user()->isController())
                                <a class="dropdown-item {{ request()->routeIs('join.public') ? 'active white-text' : '' }}" href="{{url ('/join')}}">How to Become a Vancouver Controller</a>
                            @endif
                            <a class="dropdown-item {{ request()->routeIs('policies') ? 'active white-text' : '' }}" href="{{route('policies')}}">Policies</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('airports', 'preferredrouting', 'pdc', 'vfr') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="pilotsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pilots</a>
                            <div class="dropdown-menu" aria-labelledby="pilotsDropdown">
                                <a class="dropdown-item {{ request()->routeIs('airports') ? 'active white-text' : '' }}" href="{{route('airports')}}">Airports</a>
                                <a class="dropdown-item {{ request()->routeIs('preferredrouting') ? 'active white-text' : '' }}" href="{{route('preferredrouting')}}">Preferred Routing</a>
                                <a class="dropdown-item {{ request()->routeIs('pdc') ? 'active white-text' : '' }}" href="{{ route('pdc') }}">Pre-Departure Clearance</a>
                                <a class="dropdown-item {{ request()->routeIs('vfr') ? 'active white-text' : '' }}" href="{{route('vfr')}}">VFR</a>
                                <a class="dropdown-item" href="https://vatsim.net/docs/pilots/pilots" target="_blank" rel="noopener">VATSIM Resources <i class="fas fa-arrow-up-right-from-square fa-xs ms-1 opacity-50" aria-hidden="true"></i></a>
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('meetingminutes', 'privacy') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="publicationsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Publications</a>
                            <div class="dropdown-menu" aria-labelledby="publicationsDropdown">
                                <a class="dropdown-item {{ request()->routeIs('meetingminutes') ? 'active white-text' : '' }}" href="{{route('meetingminutes')}}">Meeting Minutes</a>
                                <a class="dropdown-item {{ request()->routeIs('privacy') ? 'active white-text' : '' }}" href="{{route('privacy')}}">Privacy Policy</a>
                            </div>
                        </li>
                        <li class="nav-item  {{ Request::is('staff') ? 'active' : '' }}">
                            <a class="nav-link" href="{{url ('/staff')}}" aria-expanded="false">Staff</a>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('feedback.create', 'feedback.create.post', 'yourfeedback') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="feedbackDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Feedback</a>
                            <div class="dropdown-menu" aria-labelledby="feedbackDropdown">
                                <a class="dropdown-item {{ request()->routeIs('yourfeedback') ? 'active white-text' : '' }}" href="{{route('yourfeedback')}}">Your Feedback</a>
                                <a class="dropdown-item {{ request()->routeIs('feedback.create', 'feedback.create.post') ? 'active white-text' : '' }}" href="{{route('feedback.create')}}">Submit Feedback</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto nav-flex-icons">
                        @unless (auth()->check())
                        <li class="nav-item d-flex align-items-center">
                            <a href="{{route('auth.connect.login')}}" class="nav-link waves-effect waves-light">
                                <i class="fas fa-sign-in-alt"></i>&nbsp;Login
                            </a>
                        </li>
                        @endunless
                        @auth
                        <li class="nav-item dropdown user-nav-dropdown">
                            <a class="nav-link dropdown-toggle user-menu-toggle" href="#" role="button" id="navbarDropdownMenuLink-333" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{auth()->user()->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;">&nbsp;<span class="fw-bold"><span class="d-none d-xl-inline">Hi, </span>{{auth()->user()->fullName("F")}}<span class="d-none d-xl-inline">!</span></span>
                            </a>

                            <div class="dropdown-menu dropdown-default user-dropdown-menu" aria-labelledby="navbarDropdownMenuLink-333">
                                    <a class="dropdown-item" href="{{route('dashboard.index')}}"> <i class="fa-regular fa-user me-2"></i> Dashboard </a>
                                @if (auth()->user()->activeInstructorProfile || auth()->user()->can('training.manage'))
                                    <a class="dropdown-item" href="{{route('training.index')}}"> <i class="fas fa-chalkboard-teacher me-2"></i> Instructors </a>
                                @elseif (auth()->user()->studentProfile)
                                    <a class="dropdown-item" href="{{route('training.index')}}"> <i class="fas fa-chalkboard-teacher me-2"></i> Training </a>
                                @endif
                                    <form method="POST" action="{{route('auth.logout')}}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent text-start"> <i class="fa fa-sign-out-alt me-2"> </i> &nbsp; Logout </button>
                                    </form>
                            </div>
                        </li>
                        @endauth
                        <li class="nav-item d-flex align-items-center gap-3 gap-lg-0">
                            <a href="https://www.twitter.com/vancouverfir" class="nav-link waves-effect waves-light" target="_blank" rel="noopener" aria-label="Vancouver FIR on Twitter">
                                <i class="fab fa-twitter social-brand-icon social-outline-icon" aria-hidden="true"></i>
                            </a>
                            <a href="https://www.instagram.com/czvrfir/" class="nav-link waves-effect waves-light" target="_BLANK" >
                                <i class="fab fa-instagram social-brand-icon" aria-hidden="true"></i>
                            </a>
                            <a class="nav-link waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#discordTopModal" role="button" aria-label="Open Discord information">
                                <i class="fab fa-discord social-brand-icon social-outline-icon" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    @if ($errors->any())
        <div class="alert alert-danger" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
        </div>
    @endif
    @if (\Session::has('success'))
        <div class="alert alert-success" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('success') !!}
            </div>
        </div>
    @endif
    @if (\Session::has('error'))
        <div class="alert alert-danger" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('error') !!}
            </div>
        </div>
    @endif
    @if (\Session::has('info'))
        <div class="alert alert-info" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('info') !!}
            </div>
        </div>
    @endif
    <!--End header-->
    <!--SIDEBAR-->
    <div class="sidebar" id="czvrSidebar">
      @yield('sidebar')
    </div>
    <div id="czqoContent">
        @yield('content')
    </div>
    <!-- Footer -->
    <footer class="page-footer font-small py-4 {{Request::is('dashboard') ? 'mt-5' : ''}}">
        <div class="container">
            <p>For Flight Simulation Use Only - Not to be used for real-world navigation. By using this site, you agree to hold harmless and indemnify the owners and authors of these web pages, those listed on these pages, and all pages that this site that may be pointed to (i.e. external links).</p>
            <p>Copyright © {{ date('Y') }} Vancouver FIR | All Rights Reserved</p>
            <div class="flex-left mt-3">
            <a href="{{route('about')}}">Github</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('privacy')}}">Privacy Policy</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('branding')}}">Branding</a>
                &nbsp;
                •
                &nbsp;
                <a href="https://www.vatcan.ca" target="_blank" rel="noopener">VATCAN <i class="fas fa-arrow-up-right-from-square fa-xs ms-1 opacity-50" aria-hidden="true"></i></a>
                &nbsp;
                •
                &nbsp;
                <a href="https://www.vatsim.net" target="_blank" rel="noopener">VATSIM <i class="fas fa-arrow-up-right-from-square fa-xs ms-1 opacity-50" aria-hidden="true"></i></a>
            </div>
        </div>
    </footer>
    <!-- Footer -->
    @if (auth()->check() && auth()->user()->init == 0 && Request::is('privacy') == false)
    <!--Privacy welcome modal-->
    <div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="welcomeModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="welcomeModalTitle">Welcome to CZVR!</h5>
                </div>
                <div class="modal-body">
                    Welcome to the Vancouver FIR Website. Here you can access important pilot and controller resources! Before
                    we allow you to use the system, we require you to accept our Privacy Policy. The Policy is available
                    <a target="_blank" href="{{url('/privacy')}}">here.</a>
                    By default, you are <b>not</b> subscribed to non-essential email notifications. Head to the Dashboard and click on "Manage my preferences" to
                    subscribe - we highly recommend it!
                </div>
                <div class="modal-footer">
                    <form method="POST" action="{{ URL('/privacydeny') }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">I Disagree</button>
                    </form>
                    <form method="POST" action="{{url('/privacyaccept')}}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">I Agree</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <script>
            bootstrap.Modal.getOrCreateInstance(document.getElementById('welcomeModal'), { backdrop: 'static' }).show();
        </script>
    <!-- End privacy welcome modal-->
    @endif
    <!-- Contact us modal-->
    <div class="modal fade" id="contactUsModal" tabindex="-1" role="dialog" aria-labelledby="contactUsModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactUsModalTitle">Contact Vancouver FIR</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    To contact us, please do one of the following:
                    <ol>
                        <li>Login and open a <a href="{{route('tickets.index')}}">support ticket.</a></li>
                        <li>Head to the <a href="{{route('staff')}}">staff page</a> and email the relevant staff member.</li>
                        <li>Join our Discord server and ask in the #general channel.</li>
                    </ol>
                    <b>If your query is related to ATC coverage for your event, please visit <a href="{{route('events.index')}}">this page.</a></b>
                </div>
            </div>
        </div>
    </div>
    <!-- End contact us modal-->
    @if (\Session::has('error-modal'))
    <!-- Error modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="errorModalTitle"><span class="fw-bold red-text"><i class="fas fa-exclamation-circle"></i> An error occurred...</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>{{\Session::get('error-modal')}}</span>
                    <div class="alert alert-light mt-4">
                        <small>If you believe this is a mistake, please create a <a target="_blank" href="{{route('tickets.index')}}">support ticket.</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    bootstrap.Modal.getOrCreateInstance(document.getElementById('errorModal')).show();
    </script>
    <!-- End error modal -->
    @endif
    <!-- Start Discord (top nav) modal -->
    <div class="modal fade" id="discordTopModal" tabindex="-1" role="dialog" aria-labelledby="discordTopModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discordTopModalTitle">Join the Vancouver FIR Discord!</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>To join our Discord community, please head to your <a href="{{route('dashboard.index')}}">dashboard.</a></p>
                    <p>VATCAN has a Discord too! You can join the VATCAN Discord by clicking <a href="https://vatcan.ca/my/integrations" rel="noopener noreferrer" target="_blank">here!</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- End Discord (top nav) modal -->
    <script>
        if (window.jQuery) {
            $("blockquote").addClass('blockquote');
        }
    </script>
    <script src="{{ asset('js/form-controls.js') }}"></script>
    @stack('scripts')
    </body>
</html>
