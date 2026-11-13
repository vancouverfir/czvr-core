@extends('layouts.master')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="container" style="margin-top: 20px;">
            <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
            <h1 class="blue-text fw-bold mt-2">Feedback</h1>
            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                        Controller Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="event-tab" data-bs-toggle="tab" href="#event" role="tab" aria-controls="event" aria-selected="false">
                        Event Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Website Feedback</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if (count($controller_feedback) < 1)
                        <br>
                        No controller feedback!
                        <br><br>
                    @else
                        <br>
                        <p>There are {{count($controller_feedback)}} controller feedback submissions! <text class="fw-bold">{{count($controller_feedback_attention) == 0 ? "" :count($controller_feedback_attention).' needs your attention.'}}</text><text class="btn-link float-end" data-bs-toggle="modal" data-bs-target="#iconsModal">What do the icons mean?</text></p>
                        <table id="controllerFeedbackTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Submitter</th>
                                <th scope="col">Controller</th>
                                <th scope="col">Position</th>
                                <th scope="col">Submitted</th>
                                <th scope="col" style="text-align: center;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($controller_feedback as $f)
                                <tr>
                                    <td scope="row">{{$f->id}}</td>
                                    <td>{{ $f->user?->fullName('FLC') ?? $f->user_id }}</td>
                                    <td>{{ $f->controller?->fullName('FLC') ?? $f->controller_cid }}</td>
                                    <td>{{$f->position}}</td>
                                    <td>{{$f->created_at}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{url('/admin/feedback/controller/'.$f->id)}}"><i class="fa fa-eye"></i></a>&nbsp;
                                        <form method="POST" action="{{url('/admin/feedback/controller/'.$f->id.'/approve')}}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="border-0 bg-transparent p-0 {{ $f->approval == 2 ? 'text-success' : 'blue-text' }}"><i class="fa fa-check"></i></button>
                                        </form>&nbsp;
                                        <form method="POST" action="{{url('/admin/feedback/controller/'.$f->id.'/deny')}}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="border-0 bg-transparent p-0 {{ $f->approval == 1 ? 'text-danger' : 'blue-text' }}"><i class="fa fa-times"></i></button>
                                        </form>&nbsp;
                                        <form method="POST" action="{{url('/admin/feedback/controller/'.$f->id.'/delete')}}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border-0 bg-transparent p-0 blue-text"><i class="fa fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br>
                    @endif
                </div>
                <div class="tab-pane fade" id="event" role="tabpanel" aria-labelledby="event-tab">
                    @if (count($event_feedback) < 1)
                        <br>
                        No event feedback!
                        <br><br>
                    @else
                        <br>
                        <p>There are {{ count($event_feedback) }} event feedback submissions!</p>
                        <table id="eventFeedbackTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Submitted</th>
                                    <th scope="col" style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($event_feedback as $f)
                                    <tr>
                                        <th scope="row">{{ $f->id }}</th>
                                        <td>{{ $f->user->fullName('FLC') }}</td>
                                        <td>{{ $f->subject }}</td>
                                        <td>{{ $f->created_at }}</td>
                                        <td style="text-align: center;">
                                            <a href="{{ url('/admin/feedback/event/' . $f->id) }}"><i class="fa fa-eye"></i></a>&nbsp;
                                            <form method="POST" action="{{ url('/admin/feedback/event/' . $f->id . '/delete') }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="border-0 bg-transparent p-0 blue-text"><i class="fa fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                    @endif
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    @if (count($website_feedback) < 1)
                        <br>
                        No website feedback!
                        <br><br>
                    @else
                        <br>
                        <p>There are {{count($website_feedback) }} website feedback submissions!</p>
                        <table id="websiteFeedbackTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Author</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Submitted</th>
                                <th scope="col" style="text-align: center;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($website_feedback as $f)
                                <tr>
                                    <th scope="row">{{$f->id}}</th>
                                    <td>{{$f->user->fullName('FLC')}}</td>
                                    <td>{{$f->subject}}</td>
                                    <td>{{$f->created_at}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{url('/admin/feedback/website/'.$f->id)}}"><i class="fa fa-eye"></i></a>&nbsp;
                                        <form method="POST" action="{{url('/admin/feedback/website/'.$f->id.'/delete')}}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border-0 bg-transparent p-0 blue-text"><i class="fa fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Start Icons Modal-->
    <div class="modal fade" id="iconsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">What do the icons mean?</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>If feedback is approved, it will be live on the 'Your Feedback' page.</p>
                    <br>
                    <p><i class="fa fa-eye"></i> - View the feedback.</p>
                    <p><i class="fa fa-check"></i>&nbsp;<i class="fa fa-check text-success"></i> - Click this to approve the feedback. If it's green, it's already approved.</p>
                    <p><i class="fa fa-times"></i>&nbsp;<i class="fa fa-times text-danger"></i> - Click this to deny the feedback. If it's red, it's already denied.</p>
                    <p><i class="fa fa-trash-alt"></i> - Delete the feedback.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- End Icons Modal-->
@stop
