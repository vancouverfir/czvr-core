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
    <a href="{{route('training.index')}}">
        <img src=https://czvr.ca/storage/files/branding/czvr-logomark.png style="height:50px" > <h3 class="text-white ml-5 mb-0">Welcome {{ Auth::user()->display_fname }}!</h3>
    </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="{{route('training.index')}}">Home</a>
            </li>
            {{--<li>
                <a class="nav-link" href="{{route('training.instructingsessions.index')}}">Instructing Sessions</a>
            </li>--}}
            @if (auth()->user()->permissions >= 3)
            <li class="nav-item">
                <a class="nav-link" href="{{route('training.instructors')}}">Instructors</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{route('training.resources')}}">Resources</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color:white" href="{{ route('training.students.students') }}">Students</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" style="color:white" href="{{ route('training.students.waitlist') }}">Waitlist</a>
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
                <a class="nav-link" href="{{route('training.index')}}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('training.resources')}}">Resources</a>
            </li>
        </ul>
    </div>
</nav>
@endif
