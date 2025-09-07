@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

<style>
    #training-notes-container {
    max-height: 545px;
    overflow-y: auto;
    }
</style>

@section('content')
    <!-- Instructors -->
    @if (Auth::check() && Auth::user()->permissions >= 2)
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <h2 class="font-weight-bold blue-text">Instructor Portal</h2>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Your Students</text>
                    </div>
                    <div class="card-body">
                        @if ($yourStudents !== null && count($yourStudents) > 0)
                        <div class="list-group">
                            @foreach ($yourStudents as $student)
                            <a href="{{route('training.students.view', $student->id)}}" class="list-group-item d-flex justify-content-between align-items-center"> <img src="{{$student->user->avatar()}}" style="height: 30px; width: 30px; margin-right: 15px; border-radius: 50%;">
                                {{$student->user->fullName('FLC')}}
                                {{-- <i class="text-dark">Session planned at {date}</i> --}}
                                @if ($student->status == 4)
                                    <span class="btn-sm btn-danger">
                                        <h6 class="p-0 m-0">On Hold</h6>
                                    </span>
                                @else
                                    <span class="btn-sm btn-success">
                                        <h6 class="p-0 m-0">Open</h6>
                                    </span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        @else
                            No students are allocated to you!
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <text class="font-weight-bold">Upcoming Sessions</text>
                    </div>

                    <div class="card-body">
                        // Coming Soon!
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif (auth()->user()->studentProfile)

    <!-- User -->
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px; margin-bottom: 30px;">
        <div class="row">
            <div class="col">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-row w-100 align-items-center h-100">
                            <img src="{{$student->user->avatar()}}" style="height: 50px; width: 50px; margin-right: 15px; border-radius: 50%;">
                            <div class="d-flex flex-column w-100">
                                <h5 class="list-group-item">
                                    {{$student->user->fullName('FLC')}} - {{$student->user->rating_short}} {{$student->user->division_code}} {{$student->user->region_code}} {{$student->user->subdivision_code}}
                                    <div class="d-flex flex-wrap mt-2 justify-content-left align-items-left">
                                        @foreach($student->labels as $label)
                                            <span class="mr-2 mb-1" style="background-color: {{$label->label->color}};">
                                                {{$label->label->labelHtml()}}
                                            </span>
                                        @endforeach
                                    </div>
                                </h5>
                            </div>
                        </div>
                        <h5 class="mt-3 font-weight-bold">Instructor</h5>
                        @if ($student->instructor)
                            <div class="d-flex flex-column w-50 align-items-left">
                                <h5 class="list-group-item" style="background-color: transparent; border: none;">
                                    <img src="{{$student->instructor->user->avatar()}}" style="height: 27px; width: 27px; margin-right: 5px; border-radius: 70%;"> {{$student->instructor->user->fullName('FLC')}}
                                </h5>
                            </div>
                        @else
                            <span>No Instructor Assigned!</span>
                        @endif

                        <h5 class="mt-3 font-weight-bold">Availability</h5>
                        <span>{{$student->times ?? 'Not yet submitted!'}}</span>
                    </div>
                </div>

        <!-- Checklist -->
            <div class="card mb-3" style="max-height: 700px; overflow-y: auto;">
                <div class="card-body">
                    @if ($studentChecklistGroups->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3 sticky-top p-2" style="background-color: #2e2f2f;">
                            <h3 class="font-weight-bold blue-text mb-0">Your Checklists</h3>
                            <div class="d-flex align-items-center gap-2">
                                <button id="toggle-completed" class="btn btn-sm btn-outline-info">
                                    Hide Completed
                                </button>
                            </div>
                        </div>
                        @foreach ($studentChecklistGroups as $checklistName => $items)
                        <div class="mb-3 border p-3 rounded">
                            <div class="d-flex justify-content-center mb-3" style="min-height: 40px;">
                                <h3 class="font-weight-bold mb-0">{{ $checklistName }}</h3>
                            </div>
                            <form method="POST" action="{{ route('training.students.checklist.completeMultiple', $student->id) }}" id="checklist-form-{{ Str::slug($checklistName) }}">
                                @csrf
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                        <tr class="{{ $item->completed ? 'completed-row' : '' }}" style="{{ $item->completed ? 'text-decoration: line-through; color: grey' : '' }}">
                                            <td>{{ $item->checklistItem->item }}</td>
                                            <td class="text-center">
                                                @if ($item->completed)
                                                    Completed
                                                @else
                                                    Not Completed
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        @endforeach
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-weight-bold blue-text mb-0">Your Checklist</h3>
                        </div>
                        <span style="align-items:center;">No checklists assigned!</span>
                    @endif
                </div>
            </div>
        </div>

    <!-- Info -->
    <div class="col">
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="font-weight-bold blue-text text-primary mb-3">Info</h3>

                @if ($student->status == 3)
                    <p>There are currently <strong>{{ $Visitors }}</strong> visitors total!</p>
                    <span>Visitor training at Vancouver runs on a first come first served basis! Keep an eye out for pings on <a href="https://discord.com/channels/589477926961938443/981664706953625640" class="blue-text">#visitor-training</a>!</span>
                    <button class="btn btn-sm btn-outline-info mt-2 mb-3" id="editTimes">Edit Availability</button>
                    <hr class="bg-light">
                    <small class="d-block mb-3"> You last renewed your training request {{ $student->renewed_at?->format('F j, Y H:i') ?? 'not renewed yet' }} </small>

                @elseif ($student->status == 0)
                    @if ($waitlistPosition)
                        <p>You are currently <strong>{{ $waitlistPosition }}</strong> on the waitlist!</p>
                    @else
                        <p>You are currently not on the waitlist!</p>
                    @endif

                    <button class="btn btn-sm btn-outline-info mt-2 mb-3" id="editTimes">Edit Availability</button>

                    <small class="d-block mb-3"> You last renewed your training request {{ $student->renewed_at?->format('F j, Y H:i') ?? 'not renewed yet' }} </small>

                    <hr>

                    <p><strong>Estimated Wait Time:</strong> {{ $training_time->wait_length }}</p>

                @else
                    <h3>Your Training has Started!</h3>

                    <button class="btn btn-sm btn-outline-info mt-2 mb-3" id="editTimes">Edit Availability</button>
                @endif

                <div id="timesFormContainer" class="mt-3" style="display:none;">
                    <form method="POST" action="{{ route('training.students.editTimes', $student->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="timesInput">Session Times</label>
                            <textarea name="times" id="timesInput" rows="3" class="form-control" placeholder="Enter your availability here! Times in Z!"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="cancelTimesBtn">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vatcan Notes -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="font-weight-bold blue-text mb-0">Latest Vatcan Notes</h3>
                    <div>
                        @if ($student)
                            <a href="{{ route('training.students.allnotes', $student->id) }}"
                            class="text-primary btn btn-sm btn-outline-info ms-2">
                                View All
                            </a>
                        @endif
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    Notes left by non Vancouver FIR members not shown!
                </div>
            </div>

            <div class="card-body" id="training-notes-container">
                @php
                    $sessionTypes = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];
                    $sessionBadgeColors = ['bg-secondary', 'bg-warning', 'bg-success', 'bg-info'];
                    $afacility = [
                        'Academy', 'Edmonton FIR', 'Gander Oceanic', 'Moncton/Gander FIR',
                        'Montreal FIR', 'Toronto FIR', 'Vancouver FIR', 'Winnipeg FIR'
                    ];
                @endphp

                @if (empty($vatcanNotes))
                    <span class="alert">You have no training notes available!</span>
                @else
                    @foreach (array_slice($vatcanNotes, 0, 3) as $note)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="far fa-sticky-note mr-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        {{ $note['instructor_name'] ?? 'Unknown' }} -
                                        {{ $afacility[$note['facility_id'] ?? -1] ?? 'Unknown' }}
                                        {{ \Carbon\Carbon::parse($note['friendly_time'] ?? $note['created_at'] ?? now())->format('F j, Y') }}
                                    </h6>
                                    <div class="mt-1">
                                        @if (!empty($note['position_trained']))
                                            <span class="badge bg-info">{{ $note['position_trained'] }}</span>
                                        @endif
                                        @php $stype = $note['session_type'] ?? 3; @endphp
                                        <span class="badge {{ $sessionBadgeColors[$stype] ?? 'bg-info' }}">
                                            {{ $sessionTypes[$stype] ?? 'Generic' }}
                                        </span>
                                    </div>
                                    @if (!empty($note['training_note']))
                                        <p class="mt-3 mb-0" style="white-space: pre-wrap;">{{ $note['training_note'] }}</p>
                                    @endif

                                    @if (!empty($note['marking_sheet']))
                                        <hr class="my-3">
                                        <a href="{{ $note['marking_sheet'] }}" target="_blank"
                                        class="btn btn-outline-info btn-sm">
                                            <i class="far fa-list-alt me-1"></i> View Marking Sheet
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

    <script>
    $(document).ready(function() {
        $('.label-card').click(function() {
            $('.label-card').removeClass('selected').css('border', '1px solid transparent');
            $(this).addClass('selected');
            const labelId = $(this).data('id');
            $('#selectedLabel').val(labelId);
            $(this).css('border', '1px solid #ffffff');
        });
    });

    document.getElementById('editTimes').addEventListener('click', function() {
        document.getElementById('timesFormContainer').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelTimesBtn').addEventListener('click', function() {
        document.getElementById('timesFormContainer').style.display = 'none';
        document.getElementById('editTimes').style.display = 'inline-block';
    });

    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('toggle-completed');
        let hidden = false;

        if (button) {
            button.addEventListener('click', function () {
                const rows = document.querySelectorAll('.completed-row');
                rows.forEach(row => row.style.display = hidden ? '' : 'none');
                hidden = !hidden;
                button.textContent = hidden ? 'Show Completed' : 'Hide Completed';
            });
        }
    });
    </script>

    @else
        <div class="d-flex justify-content-center align-items-center min-vh-100" style="margin-top: 50px; margin-bottom: 50px;">
            <div class="text-center" style="max-width: 700px; width: 100%;">
                <h1 class="font-weight-bold blue-text mb-3">
                    <i class="fas fa-info-circle"></i> Uh Oh! 👀
                </h1>

                <p style="font-size: 1.2em; margin-bottom: 1.5rem;">
                    You’ve wandered into a page reserved for home and visiting controllers only!<br><br>
                    Don’t worry — here are some useful links to get you back on track!
                </p>

                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <a href="{{ route('trainingtimes') }}" class="btn btn-outline-primary">
                        <i class="fas fa-book"></i> Training FAQ
                    </a>
                    <a href="{{ route('join.public') }}" class="btn btn-outline-success">
                        <i class="fas fa-plane-departure"></i> Join Our FIR
                    </a>
                    <a href="{{ route('staff') }}" class="btn btn-outline-info">
                        <i class="fas fa-envelope"></i> Contact Staff
                    </a>
                </div>
            </div>
        </div>
    @endif

@stop
