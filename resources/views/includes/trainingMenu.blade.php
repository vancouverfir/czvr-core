<style>
    .nav-link {
        color: white !important;
    }
    .navbar a {
        display: flex;
        align-items: center;
    }
</style>
@if (Auth::user()->instructorProfile !== null || Auth::user()->permissions >= 3)
<nav class="navbar navbar-light bg-dark">
    <div class="container">
    <a href="/dashboard/training">
        <img src=https://czvr.ca/storage/files/branding/czvr-logomark.png style="height:50px" > <h3 class="text-white ml-5 mb-0">Hello, {{ Auth::user()->display_fname }}!</h3>
    </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training') ? 'active' : '' }}" href="{{route('training.index')}}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/instructors/*') || Request::is('dashboard/training/instructors') ? 'active' : '' }}" href="{{route('training.instructors')}}">Instructors</a>
            </li>
            <li>
                <a class="nav-link {{Request::is(route('training.instructingsessions.index')) ? 'active' : ''}}" href="{{route('training.instructingsessions.index')}}">Instructing Sessions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/students/students') ? 'active' : '' }}" style="color:white" href="{{ route('training.students.students') }}">
                    Students
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') ? 'active' : '' }}" href="{{route('training.applications')}}">
                    Applications
                    @if (count(\App\Models\AtcTraining\Application::where('status', 0)->get()) >= 1)
                        <span class="badge-pill {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') ? 'badge-light text-primary' : 'badge-primary' }}">{{count(\App\Models\AtcTraining\Application::where('status', 0)->get())}}</span>
                    @endif
                </a>
            </li>
            {{-- <li class="nav-item dropdown">
                <a class="nav-link {{ Request::is('dashboard/training/cbt') ? 'active' : '' }}" href="{{route('cbt.index')}}">CBT System</a> --}}
            @endif
        </ul>
    </div>
</nav>
