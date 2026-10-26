@once
    @push('styles')
        <link href="{{ asset('css/pages/training-shell.css') }}" rel="stylesheet">
    @endpush
@endonce

@php
    $isTrainingStaff = auth()->user()->activeInstructorProfile !== null || auth()->user()->can('training.manage');
@endphp

<nav class="navbar navbar-light training-navbar" aria-label="Training portal">
    <div class="container training-navbar-inner">
        <a class="training-navbar-brand" href="{{ route('training.index') }}">
            <img src="{{ asset('CZVR_Mark.svg') }}" alt="Vancouver FIR">
            <span class="training-navbar-brand-copy">
                Welcome, {{ auth()->user()->display_fname }}!
                <small>Training Centre</small>
            </span>
        </a>

        <div class="training-nav-scroll">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('training.index') ? 'active' : '' }}" href="{{ route('training.index') }}">Home</a>
                </li>

                @can('instructing-sessions.manage')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('training.instructingsessions.*') ? 'active' : '' }}" href="{{ route('training.instructingsessions.index') }}">Sessions</a>
                    </li>
                @endcan

                @if ($isTrainingStaff)
                    @can('training.instruct')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training.instructors') ? 'active' : '' }}" href="{{ route('training.instructors') }}">Instructors</a>
                        </li>
                    @endcan
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('training.resources', 'atcresources.*') ? 'active' : '' }}" href="{{ route('training.resources') }}">Resources</a>
                </li>

                @if ($isTrainingStaff)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('training.students.students', 'training.students.view', 'training.students.completed') ? 'active' : '' }}" href="{{ route('training.students.students') }}">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('training.students.waitlist') ? 'active' : '' }}" href="{{ route('training.students.waitlist') }}">Waitlist</a>
                    </li>
                @endif

                @can('training.stats')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('training.stats') ? 'active' : '' }}" href="{{ route('training.stats') }}">Statistics</a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</nav>
