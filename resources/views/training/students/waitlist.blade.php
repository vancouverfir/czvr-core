@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@push('styles')
    <link href="{{ asset('css/pages/training-waitlist.css') }}" rel="stylesheet">
@endpush

@section('content')
@include('partials.vendor.select2')
    @include('includes.trainingMenu')

    <main class="container training-page">
        <header class="training-page-header">
            <div>
                <h1 class="blue-text">Waitlist</h1>
                <p>Manage home and visiting controllers waiting for training!</p>
            </div>
            <button type="button" data-bs-toggle="modal" data-bs-target="#newStudent" class="btn btn-sm btn-primary">
                <i class="fa fa-plus me-1"></i>Add student
            </button>
        </header>
        @php
            $queues = [
                [
                    'body' => 'waitlistTableBody',
                    'label' => 'Students',
                    'empty' => 'There are no students in the waitlist!',
                    'items' => $waitlistStudents,
                ],
                [
                    'body' => 'visitorWaitlistTableBody',
                    'label' => 'Visitors',
                    'empty' => 'There are no visitors in the waitlist!',
                    'items' => $visitorWaitlist,
                ],
            ];
        @endphp

        <div class="waitlist-legend" aria-label="Availability status">
            <span><span class="dot blue-dot" aria-hidden="true"></span>Availability submitted</span>
            <span><span class="dot purple-dot" aria-hidden="true"></span>Availability not submitted</span>
        </div>

        <div class="waitlist-queue-grid">
            @foreach ($queues as $queue)
                <section class="waitlist-queue">
                    <div class="waitlist-queue-header">
                        <div>
                            <div class="waitlist-queue-title">
                                <h2>{{ $queue['label'] }}</h2>
                                <span>{{ $queue['items']->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive waitlist-table-scroll">
                        <table class="table waitlist-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Controller</th>
                                    <th scope="col" class="waitlist-availability-heading" title="Availability">
                                        <span class="visually-hidden">Availability</span>
                                    </th>
                                    <th scope="col">Contact</th>
                                </tr>
                            </thead>
                            <tbody id="{{ $queue['body'] }}" class="waitlist-sortable-body">
                                    @forelse ($queue['items'] as $student)
                                        @php
                                            $contactEvent = $student->latestWaitlistContactEvent;
                                            $isContacted = $contactEvent?->title === 'Waitlist Contacted';
                                            $hasAvailability = filled($student->availability);
                                        @endphp
                                        <tr data-id="{{ $student->id }}">
                                            <th scope="row" class="waitlist-position {{ auth()->user()->can('training.instruct') ? 'drag-sortable' : '' }}">
                                                @can('training.instruct')
                                                    <i class="fas fa-grip-vertical" aria-hidden="true"></i>
                                                @endcan
                                                <span class="waitlist-position-number">{{ $loop->iteration }}</span>
                                            </th>
                                            <td>
                                                <span class="waitlist-person-heading">
                                                    @if (! $student->user->rosterProfile)
                                                        <span class="badge text-bg-danger waitlist-vatcan-warning" title="User is not certified / not on roster">NOT ON VATCAN</span>
                                                    @endif
                                                    <a href="{{ route('training.students.view', $student->id) }}" class="waitlist-person-link">{{ $student->user->fullName('FL') }}</a>
                                                </span>
                                                <span class="waitlist-cid">CID {{ $student->user->id }}</span>
                                            </td>
                                            <td>
                                                <span class="waitlist-availability" title="{{ $hasAvailability ? 'Availability submitted' : 'Availability not submitted' }}">
                                                    <span class="dot {{ $hasAvailability ? 'blue-dot' : 'purple-dot' }}" aria-hidden="true"></span>
                                                    <span class="visually-hidden">{{ $hasAvailability ? 'Availability submitted' : 'Availability not submitted' }}</span>
                                                </span>
                                            </td>
                                            <td class="waitlist-contact-cell">
                                                @if (auth()->user()->can('training.instruct'))
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-link p-0 border-0 shadow-none dropdown-toggle waitlist-email-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            {{ $student->user->email }}
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end text-start">
                                                            <li><a class="dropdown-item" href="mailto:{{ $student->user->email }}"><i class="far fa-envelope me-2"></i>Send email</a></li>
                                                            @php($discordUsername = $student->user->discord_user_id ? $student->user->getDiscordUser()?->username : null)
                                                            @if ($discordUsername)
                                                                <li><span class="dropdown-item-text user-select-all"><i class="fab fa-discord social-outline-icon me-2"></i>{{ $discordUsername }}</span></li>
                                                            @else
                                                                <li><span class="dropdown-item disabled text-muted"><i class="fab fa-discord social-outline-icon me-2"></i>{{ $student->user->discord_user_id ? 'Discord username unavailable' : 'Discord not linked' }}</span></li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            @if ($isContacted)
                                                                <li class="px-3 py-1">
                                                                    <span class="waitlist-contact-state">
                                                                        Contacted by {{ $contactEvent->author?->fullName('FL') ?? 'Unknown' }}
                                                                        <time datetime="{{ $contactEvent->created_at->toIso8601String() }}">
                                                                            {{ $contactEvent->created_at->isCurrentYear() ? $contactEvent->created_at->format('M j · H:i') : $contactEvent->created_at->format('M j, Y · H:i') }} UTC
                                                                        </time>
                                                                    </span>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" action="{{ route('training.students.waitlist.contact', $student) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="contacted" value="0">
                                                                        <button type="submit" class="dropdown-item waitlist-contact-reset"><i class="fas fa-undo me-2"></i>Clear contact status</button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form method="POST" action="{{ route('training.students.waitlist.contact', $student) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="contacted" value="1">
                                                                        <button type="submit" class="dropdown-item"><i class="fas fa-check me-2"></i>Mark as contacted</button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">Hidden for privacy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="waitlist-empty">{{ $queue['empty'] }}</td></tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>
    </main>

    <div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-labelledby="newStudentTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('instructor.student.add.new') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="newStudentTitle">Add New Student</h5>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">Visitors and students are imported from VATCAN. Manual creation is only needed in exceptional cases.</p>
                        <label class="form-label" for="student_id">Student</label>
                        <select name="student_id" id="student_id" class="js-example-basic-single form-select" required style="width:100%;">
                            <option value="">No Student</option>
                            @foreach ($potentialstudent as $u)
                                <option value="{{ $u->id }}">{{ $u->id }} - {{ $u->fullName('FL') }}</option>
                            @endforeach
                        </select>

                        <label class="form-label mt-3" for="instructor">Instructor</label>
                        <select name="instructor" id="instructor" class="js-example-basic-single form-select" style="width:100%;">
                            <option value="">No Instructor</option>
                            @foreach ($instructors as $i)
                                <option value="{{ $i->id }}">{{ $i->user->id }} - {{ $i->user->fullName('FL') }}</option>
                            @endforeach
                        </select>

                        <label class="form-label mt-3" for="is_visitor">Controller type</label>
                        <select name="is_visitor" id="is_visitor" class="form-select" required>
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                        <div id="visitor_type_group" style="display: none;">
                            <label class="form-label mt-3" for="visitor_type">Visitor type</label>
                            <select name="visitor_type" id="visitor_type" class="form-select">
                                <option value="vatcan">VATCAN Visitor</option>
                                <option value="non_vatcan">Non-VATCAN Visitor</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Add Student</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <link href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $('#student_id').select2({
            width: '100%',
            placeholder: "Student",
            dropdownParent: $('#newStudent')
        });

        $('#instructor').select2({
            width: '100%',
            placeholder: "No Instructor",
            allowClear: true,
            dropdownParent: $('#newStudent')
        });

        $('#is_visitor').on('change', function () {
            if ($(this).val() === '1') {
                $('#visitor_type_group').show();
            } else {
                $('#visitor_type_group').hide();
                $('#visitor_type').val('');
            }
        });

    @can('training.instruct')

        $(function () {
            function makeQueueSortable(bodySelector, url, errorMessage) {
                const body = $(bodySelector);

                body.sortable({
                    items: '> tr[data-id]',
                    handle: '.drag-sortable',
                    update: function () {
                        let order = [];

                        body.children('tr[data-id]').each(function (index) {
                            $(this).find('.waitlist-position-number').text(index + 1);

                            order.push({
                                id: $(this).data('id'),
                                position: index + 1
                            });
                        });

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                order: order
                            },
                            error: function (xhr) {
                                alert(errorMessage);
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            }

            makeQueueSortable('#waitlistTableBody', '{{ route("waitlist.sort") }}', 'Student sort failed.');
            makeQueueSortable('#visitorWaitlistTableBody', '{{ route("visitor.sort") }}', 'Visitor sort failed.');
        });
    @endcan
    </script>
@endpush
