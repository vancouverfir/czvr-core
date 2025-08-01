<style>
    .nav-link {
        color: white !important;
    }
    .navbar a {
        display: flex;
        align-items: center;
    }
</style>

@if (Auth::user()->instructorProfile !== null || Auth::user()->permissions >= 2)
<nav class="navbar navbar-light bg-dark">
    <div class="container">
    <a href="/training">
        <img src=https://czvr.ca/storage/files/branding/czvr-logomark.png style="height:50px" > <h3 class="text-white ml-5 mb-0">Welcome {{ Auth::user()->display_fname }}!</h3>
    </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training') }}" href="{{route('training.index')}}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/instructors/*') || Request::is('dashboard/training/instructors') }}" href="{{route('training.instructors')}}">Instructors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/resources') || Request::is('dashboard/training/resources')}}" href="{{route('training.resources')}}">Resources</a>
            </li>
            {{--<li>
                <a class="nav-link {{Request::is(route('training.instructingsessions.index'))}}" href="{{route('training.instructingsessions.index')}}">Instructing Sessions</a>
            </li>--}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/students/students') }}" style="color:white" href="{{ route('training.students.students') }}">Students</a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/students/waitlist') }}" style="color:white" href="{{ route('training.students.waitlist') }}">Waitlist</a>
            </li>

            {{--<li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') }}" href="{{route('training.applications')}}">
                    Applications
                    @if (count(\App\Models\AtcTraining\Application::where('status', 0)->get()) >= 1)
                        <span class="badge-pill {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') ? 'badge-light text-primary' : 'badge-primary' }}">{{count(\App\Models\AtcTraining\Application::where('status', 0)->get())}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link {{ Request::is('dashboard/training/cbt') }}" href="{{route('cbt.index')}}">CBT System</a> --}}
            </li>
        </ul>
    </div>
</nav>
@else
<nav class="navbar navbar-light bg-dark">
    <div class="container">
        <a href="/training">
            <img src=https://czvr.ca/storage/files/branding/czvr-logomark.png style="height:50px" > <h3 class="text-white ml-5 mb-0">Welcome {{ Auth::user()->display_fname }}!</h3>
        </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training') }}" href="{{route('training.index')}}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/resources') || Request::is('dashboard/training/resources')}}" href="{{route('training.resources')}}">Resources</a>
            </li>
        </ul>
    </div>
</nav>
@endif
