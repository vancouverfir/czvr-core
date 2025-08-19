@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

<style>
    .select2-container--default .select2-selection--single {
        background-color: #333 !important;
        color: #fff !important;
        border: 1px solid #777 !important;
    }
    .select2-dropdown {
    background-color: #333 !important;
    color: #fff !important;
    }
    .select2-search__field {
        background-color: #333 !important;
        color: #fff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff !important;
    }
    .select2-container--default .select2-selection__clear {
        color: red !important;
        font-size: 16px;
    }
    .dot {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .purple-dot {
        background-color: purple;
    }
    .blue-dot {
        background-color: blue;
    }
</style>

@section('content')
    @include('includes.trainingMenu')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"; rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js";></script>

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <div class="container" style="margin-top: 30px;">
        <h1 class="blue-text">
            Waitlists
            <a href="#" data-toggle="modal" data-target="#newStudent" class="text-primary" style="font-size: 18px; text-decoration: none; float: right;"><i class="fa fa-plus mr-1"></i>Create New</a>
        </h1>
        <div class="mt-1">
            @if (Auth::user()->permissions >= 3)
                <div class="text-muted small">Tip: Hover over the # column to reorder!</div>
                <style>.drag-sortable {cursor: move;}</style>
            @endif
        </div>
        <hr class="bg-light">
        <div> <span class="dot blue-dot"></span> Times submitted &nbsp;&nbsp;&nbsp; <span class="dot purple-dot"></span> Times not submitted </div>
        <div class="row border p-3 rounded" style="margin-top: 30px; margin-bottom: 30px;">
            <!-- Student Waitlist -->
            <div class="col-md-6">
                <h1 class="font-weight-bold text-light">
                    Student Waitlist
                </h1>
                <hr>
                <table id="waitlistTable" class="table">
                    <thead class="thead">
                        <tr class="text-center">
                            <th>#</th>
                            <th>CID</th>
                            <th></th>
                            <th>Student Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="waitlistTableBody">
                        @if ($waitlistStudents->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center font-weight-bold">There are no Students in the waitlist!</td>
                        </tr>
                        @else
                            @foreach ($waitlistStudents as $index => $student)
                            <tr class="text-center" data-id="{{ $student->id }}">
                                <th scope="row" class="drag-sortable">{{ $loop->iteration }}</th>
                                <td>{{ $student->user->id }}</td>
                                <td>
                                    @if(is_null($student->times))
                                        <span class="dot purple-dot"></span>
                                    @else
                                        <span class="dot blue-dot"></span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('training.students.view', $student->id) }}" class="font-weight-bold text-primary blue-text">
                                        {{ $student->user->fullName('FL') }}
                                    </a>
                                </td>
                                <td>
                                    @if (Auth::user()->permissions >= 3)
                                        {{ $student->user->email }}
                                    @else
                                        <i>Hidden for Privacy</i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Visitor Waitlist -->
            <div class="col-md-6">
                <h1 class="font-weight-bold text-light">Visitor Waitlist</h1>
                <hr>
                <table id="visitorWaitlistTable" class="table">
                    <thead class="thead">
                        <tr class="text-center">
                            <th>#</th>
                            <th>CID</th>
                            <th></th>
                            <th>Visitor Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    @if ($visitorWaitlist->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center font-weight-bold">There are no Visitors in the waitlist!</td>
                    </tr>
                    @else
                        @foreach ($visitorWaitlist as $visitor)
                        <tr class="text-center" data-id="{{ $visitor->id }}">
                            <th scope="row" class="drag-sortable">{{ $loop->iteration }}</th>
                            <td>{{ $visitor->user->id }}</td>
                            <td>
                                @if(is_null($visitor->times))
                                    <span class="dot purple-dot"></span>
                                @else
                                    <span class="dot blue-dot"></span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('training.students.view', $visitor->id) }}" class="font-weight-bold text-primary blue-text">
                                    {{ $visitor->user->fullName('FL') }}
                                </a>
                            </td>
                            <td>
                                @if (Auth::user()->permissions >= 3)
                                    {{ $visitor->user->email }}
                                @else
                                    <i>Hidden for Privacy</i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('instructor.student.add.new') }}" class="form-group">
                        @csrf
                        <small class="text-center">Visitors and Students are automatically imported from VATCAN, manual creation is only necessary in exceptional cases</small>
                        <label class="form-control font-weight-bold">Search for a Student</label>
                        <select name="student_id" id="student_id" class="js-example-basic-single form-control" required style="width:100%;">
                                <option value="">No Student</option>
                            @foreach ($potentialstudent as $u)
                                <option value="{{ $u->id }}">{{ $u->id }} - {{ $u->fullName('FL') }}</option>
                            @endforeach
                        </select>

                        <label class="form-control font-weight-bold">Assign an Instructor?</label>
                        <select name="instructor" id="instructor" class="js-example-basic-single form-control" style="width:100%;">
                                <option value="">No Instructor</option>
                            @foreach ($instructors as $i)
                                <option value="{{ $i->id }}">{{ $i->user->id }} - {{ $i->user->fullName('FL') }}</option>
                            @endforeach
                        </select>

                        <label class="form-control font-weight-bold">Is Visitor?</label>
                        <select name="is_visitor" id="is_visitor" class="form-control" required>
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                        <div id="visitor_type_group" style="display: none;">
                            <label class="form-control font-weight-bold">Visitor Type</label>
                            <select name="visitor_type" id="visitor_type" class="form-control">
                                <option value="vatcan">VATCAN Visitor</option>
                                <option value="non_vatcan">Non-VATCAN Visitor</option>
                            </select>
                        </div>
                </div>
                    <div class="modal-footer">
                        <button class="btn btn-success form-control" type="submit">Add Student</button>
                        <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                    </div>
                </form>
        </div>
    </div>
    <script>
        $('#student_id').select2({
            width: '100%',
            placeholder: "Student"
        });

        $('#instructor').select2({
            width: '100%',
            placeholder: "No Instructor",
            allowClear: true
        });

        $('#is_visitor').on('change', function () {
            if ($(this).val() === '1') {
                $('#visitor_type_group').show();
            } else {
                $('#visitor_type_group').hide();
                $('#visitor_type').val('');
            }
        });

    @if (Auth::user()->permissions >= 3)

        $(function () {
            $('#waitlistTable tbody').sortable({
                handle: '.drag-sortable',
                update: function () {
                    let order = [];

                    $('#waitlistTable tbody tr').each(function (index) {
                        $(this).find('th').text(index + 1);

                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        url: '{{ route("waitlist.sort") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function () {
                            console.log('Order updated successfully.');
                        },
                        error: function (xhr) {
                            alert('Sort failed.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            $('#visitorWaitlistTable tbody').sortable({
                handle: '.drag-sortable',
                update: function () {
                    let order = [];

                    $('#visitorWaitlistTable tbody tr').each(function (index) {
                        $(this).find('th').text(index + 1);

                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        url: '{{ route("visitor.sort") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function () {
                            console.log('Visitor waitlist order updated successfully.');
                        },
                        error: function (xhr) {
                            alert('Visitor sort failed.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    @endif
    </script>
@stop
