<!DOCTYPE HTML>
<html lang="en">
    <head>
        <!--
        {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_name}}
        {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->release}} ({{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_build}})
        Built on Bootstrap 4 and Laravel 6

        Written by Liesel D... edited by a hundred Vancouverers

        For Flight Simulation Use Only - Not to be used for real-world navigation. All content on this web site may not be shared, copied, reproduced or used in any way without prior express written consent of Vancouver FIR. © Copyright {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->copyright_year}} Vancouver FIR , All Rights Reserved.
        -->

        <!--Metadata-->

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- <link rel="shortcut icon" href="{{ asset('holiday.ico') }}" type="image/x-icon"> -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#6CC24A">
        <title>@yield('title', 'Vancouver FIR')</title>
        <meta name="description" content="@yield('description', '')">
        <meta name="theme-color" content="#6CC24A">
        <meta name="og:title" content="@yield('title', 'Vancouver FIR')">
        <meta name="og:description" content="@yield('description', '')">
        <meta name="og:image" content="@yield('image','https://cdn.discordapp.com/attachments/800588233570123776/1051930179821391912/Wordmark_Colour.png')">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- <link rel="shortcut icon" href="{{ asset('holiday.ico') }}" type="image/x-icon"> -->
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Lato&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato&display=swap">
        </noscript>
        <link rel="preload" as="style" href="{{ asset('css/bootstrap.min.css') }}" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" as="style" href="{{ asset('css/mdb.min.css') }}" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" as="style" href="{{ asset('css/all.css') }}" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" as="style" href="{{ asset('css/main.css') }}" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
            <link rel="stylesheet" href="{{ asset('css/mdb.min.css') }}">
            <link rel="stylesheet" href="{{ asset('css/all.css') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        </noscript>
        <!-- Font Awesome -->
        <link href="{{ asset('css/all.css') }}" rel="stylesheet">
        <!-- Bootstrap core CSS -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Material Design Bootstrap -->
        <link href="{{ asset('css/mdb.min.css') }}" rel="stylesheet">
        <!--SimpleMDE Editor-->
        <link href="{{ asset('css/simplemde.min.css') }}" rel="stylesheet">
        <script src="{{ asset('js/simplemde.min.js') }}" defer></script>
        <!-- JQuery -->
        <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
        <!-- Bootstrap tooltips -->
        <script type="text/javascript" src="{{ asset('js/popper.min.js') }}" defer></script>
        <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}" defer></script>
        <!-- MDB core JavaScript -->
        <script type="text/javascript" src="{{ asset('js/mdb.min.js') }}" defer></script>
        <!--CZQO specific CSS-->
        @if (Auth::check())
        @switch (Auth::user()->preferences)
            @case("default")
            <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
            @break
            @default
            <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
        @endswitch
        @else
        <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
        @endif
        <!--Leaflet-->
        <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="{{asset('/js/leaflet.rotatedMarker.js')}}" defer></script>
        <!--TinyMCE-->
        <script src="{{ asset('js/tinymce.min.js') }}" referrerpolicy="origin" defer></script>
        <!--DataTables-->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.min.css') }}"/>
        <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
        <!--CSS Emoticons-->
        <link href="{{asset('css/jquery.cssemoticons.css')}}" media="screen" rel="stylesheet" type="text/css" />
        <script src="{{asset('/js/jquery.cssemoticons.js')}}" type="text/javascript" defer></script>
        <!--Fullcalendar-->
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js" defer></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js" defer></script>
        <noscript><link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"></noscript>
        <!--IntroJS-->
        <link rel="stylesheet" href="{{asset('introjs/introjs.min.css')}}">
        <script src="{{asset('introjs/intro.min.js')}}" defer></script>
        <!--Date picker-->
        <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
        <script src="{{ asset('js/flatpickr.min.js') }}" defer></script>
        <!--SimpleMDE-->
        <link rel="stylesheet" href="{{ asset('css/simplemde.min.css') }}">
        <script src="{{ asset('js/simplemde.min.js') }}" defer></script>
        <!--Dropzone-->
        <script src="{{asset('js/dropzone.js')}}"></script>
        <!--JqueryValidate-->
        <script src="{{ asset('js/jquery.validate.min.js') }}" defer></script>
        <!---->
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
    </head>
    <body class="background">
    <!--Header-->
    <!-- <script src="/js/snowflakes.js"></script> -->
    @if (\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->banner)
        <div class="alert alert-{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->bannerMode}}" style="margin: 0; border-radius: 0; border: none;">
            <div class="text-center align-self-center">
                <a href="{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->bannerLink}}" target="_blank"><span style="margin: 0;">{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->banner}}</span></a>
            </div>
        </div>
    @endif
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark p-0 main-colour" style="min-height:59px">
            <div class="container">
                <a class="navbar-brand" href="{{route('index')}}"><img style="height: 35px; width:auto; vertical-align:inherit;" src="{{ asset('CZVR_Colour_Long.png') }}" alt=""></a>
                <!-- <a class="navbar-brand" href="{{route('index')}}"><img style="height: 35px; width:auto; vertical-align:inherit;" src="{{ asset('Holiday Long.png') }}" alt=""></a> -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        {{-- <li class="nav-item">
                            <a href="{{route('controllerbookings.public')}}" class="nav-link {{ Request::is('bookings/*') || Request::is('bookings') ? 'active' : '' }}">Bookings</a>
                        </li> --}}
                        <li class="nav-item {{ Request::is('news/*') || Request::is('news') ? 'active' : '' }}">
                            @if(Auth::check() && Auth::user()->permissions >= 4)
                            <li class="nav-item dropdown {{ Request::is('news') || Request::is('news/*') || Request::is('news') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">News</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="{{route('news')}}">News</a>
                                <a class="dropdown-item {{ Request::is('news') ? 'active white-text' : '' }}" href="{{route('news.index')}}">Manage News</a>
                            @else
                                <a href="{{route('news')}}" class="nav-link">News</a>
                            @endif
                        </li>
                        <li class="nav-item {{ Request::is('events/*') || Request::is('events') ? 'active' : '' }}">
                          <!--  @if(Auth::check() && Auth::user()->permissions >= 4)
                            <li class="nav-item dropdown {{ Request::is('events') || Request::is('events/*') || Request::is('events') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Events</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="{{route('events.index')}}">Events</a>
                                <a class="dropdown-item {{ Request::is('events') ? 'active white-text' : '' }}" href="{{route('events.admin.index')}}">Manage Events</a>
                            @else
                                <a href="{{route('events.index')}}" class="nav-link">Events</a>
                            @endif
                                                Hide as we will try and automatically fetch events-->
                            <a href="{{route('events.index')}}" class="nav-link">Events</a>
                        </li>
                        <li class="nav-item dropdown {{ Request::is('dashboard/applicationdashboard/application') || Request::is('dashboard/application/*') || Request::is('atcresources') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ATC</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            {{--<a class="dropdown-item" href="{{route('controllerbookings')}}">Bookings</a>--}}
                            <a class="dropdown-item" href="{{route('roster.public')}}">Roster</a>
                            @if(Auth::check() && Auth::user()->permissions >= 3)
                                <a class="dropdown-item {{ Request::is('roster') ? 'active white-text' : '' }}" href="{{route('roster.index')}}">Manage Roster</a>
                            @endif
                            @if(!Auth::check() || Auth::user()->permissions == 0)
                                <a class="dropdown-item {{ Request::is('join') ? 'active white-text' : '' }}" href="{{url ('/join')}}">How to Become a Vancouver Controller</a>
                            @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ Request::is('airports') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pilots</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item" href="{{route('airports')}}">Airports</a>
                                <a class="dropdown-item" href="{{route('livemap')}}">Live Map</a>
                                <a class="dropdown-item" href="{{route('vfr')}}">VFR</a>
                                <a class="dropdown-item" href="https://vatsim.net/docs/pilots/pilots" target="_blank">VATSIM Resources</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Publications</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item {{ Request::is('policies') ? 'active white-text' : '' }}" href="{{route('policies')}}">Policies</a>
                                <a class="dropdown-item {{ Request::is('meetingminutes') ? 'active white-text' : '' }}" href="{{route('meetingminutes')}}">Meeting Minutes</a>
                                <a class="dropdown-item {{ Request::is('privacy') ? 'active white-text' : '' }}" href="{{route('privacy')}}">Privacy Policy</a>
                            </div>
                        </li>
                        <li class="nav-item  {{ Request::is('staff') ? 'active' : '' }}">
                            <a class="nav-link" href="{{url ('/staff')}}" aria-expanded="false">Staff</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Feedback</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item {{ Request::is('feedback') ? 'active white-text' : '' }}" href="{{route('feedback.create')}}">Submit Feedback</a>
                                <a class="dropdown-item {{ Request::is('yourfeedback') ? 'active white-text' : '' }}" href="{{route('yourfeedback')}}">Your Feedback</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto nav-flex-icons">
                        @unless (Auth::check())
                        <li class="nav-item d-flex align-items-center">
                            {{-- <a href="{{route('auth.connect.login')}}" class="nav-link waves-effect waves-light">
                                <i class="fas fa-sign-in-alt"></i>&nbsp;Login
                            </a> --}}
                            <a href="{{route('auth.connect.login')}}" class="nav-link waves-effect waves-light">
                                <i class="fas fa-sign-in-alt"></i>&nbsp;Login
                            </a>
                        </li>
                        @endunless
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{Auth::user()->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;">&nbsp;<span class="font-weight-bold">{{Auth::user()->fullName("F")}}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right dropdown-default py-0" aria-labelledby="navbarDropdownMenuLink-333">
                                <a class="dropdown-item {{ Request::is('dashboard') || Request::is('dashboard/*')}}" href="{{route('dashboard.index')}}">
                                    <i class="fa fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a class="dropdown-item red-text" href="{{route('auth.logout')}}">
                                    <i class="fa fa-sign-out-alt mr-2"></i>&nbsp;Logout
                                </a>
                            </div>
                        </li>
                        @endauth
                        <li class="nav-item d-flex align-items-center">
                            <a href="https://mobile.twitter.com/vancouverfir" class="nav-link waves-effect waves-light" target="_BLANK" >
                                <i style="font-size: 1.7em;" class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link waves-effect waves-light" data-toggle="modal" data-target="#discordTopModal" target="_BLANK" >
                                <i style="height: 22px; font-size: 1.7em;width: 28px;padding-left: 5px;padding-top: 2px;" class="fab fa-discord"></i>
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
    <div class="sidebar" id="cywgSidebar">
      @yield('sidebar')
    </div>
    <div id="czqoContent">
        @yield('content')
    </div>
    <!-- Footer -->
    <footer class="page-footer text-light font-small py-4 {{Request::is('/dashboard') ? 'mt-5' : ''}}">
        <div class="container">
            <p style="color:white">For Flight Simulation Use Only - Not to be used for real-world navigation. By using this site, you agree to hold harmless and indemnify the owners and authors of these web pages, those listed on these pages, and all pages that this site that may be pointed to (i.e. external links).</p>
            <p style="color:white">Copyright © {{ date('Y') }} Vancouver FIR | All Rights Reserved.</p>
            <div class="flex-left mt-3">
            <a href="{{route('about')}}">Github</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('feedback.create')}}">Feedback</a>
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
                <a href="https://www.vatcan.ca">VATCAN</a>
                &nbsp;
                •
                &nbsp;
                <a href="https://www.vatsim.net">VATSIM</a>
            </div>
        </div>
    </footer>
    <!-- Footer -->
    @if (Auth::check() && Auth::user()->init == 0 && Request::is('privacy') == false)
    <!--Privacy welcome modal-->
    <div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Welcome to CZVR!</b></h5>
                </div>
                <div class="modal-body">
                    Welcome to the Vancouver FIR Website. Here you can access important pilot and controller resources! Before
                    we allow you to use the system, we require you to accept our Privacy Policy. The Policy is available
                    <a target="_blank" href="{{url('/privacy')}}">here.</a>
                    By default, you are <b>not</b> subscribed to non-essential email notifications. Head to the Dashboard and click on "Manage my preferences" to
                    subscribe - we highly recommend it!
                </div>
                <div class="modal-footer">
                    <a role="button" href="{{ URL('/privacydeny') }}" class="btn btn-outline-danger">I Disagree</a>
                    <a href="{{url('/privacyaccept')}}" role="button" class="btn btn-success">I Agree</a>
                </div>
            </div>
        </div>
    </div>
        <script>
            $('#welcomeModal').modal({backdrop: 'static'});
            $('#welcomeModal').modal('show');
        </script>
    <!-- End privacy welcome modal-->
    @endif
    <!-- Contact us modal-->
    <div class="modal fade" id="contactUsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Contact CZWG</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><span class="font-weight-bold red-text"><i class="fas fa-exclamation-circle"></i> An error occurred...</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="white-text">{{\Session::get('error-modal')}}</span>
                    <div class="alert black-text bg-white mt-4">
                        <small>If you believe this is a mistake, please create a <a target="_blank" class="black-text" href="{{route('tickets.index')}}">support ticket.</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $("#errorModal").modal();
    </script>
    <!-- End error modal -->
    @endif
    <!-- Start Discord (top nav) modal -->
    <div class="modal fade" id="discordTopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Join the Vancouver FIR Discord!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>To join our Discord community, please head to your <a href="{{route('dashboard.index')}}">dashboard.</a></p>
                    <p>VATCAN has a Discord too! You can join the VATCAN discord by clicking <a href="https://vatcan.ca/my/integrations" rel="noopener noreferrer" target="_blank">here!</a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Discord (top nav) modal -->
    <script type="text/javascript">
        Dropzone.options.dropzone =
            {
                maxFilesize: 12,
                renameFile: function (file) {
                    var dt = new Date();
                    var time = dt.getTime();
                    return time + file.name;
                },
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                timeout: 5000,
                success: function (file, response) {
                    console.log(response);
                },
                error: function (file, response) {
                    return false;
                }
            };
    </script>
    <script>
        $("blockquote").addClass('blockquote');
    </script>
    </body>
</html>
