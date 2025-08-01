@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h2 class="font-weight-bold blue-text">All Controller Applications</h2>
        <hr>
        @if ($pendingapplications)
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Pending
                        @if ($pendingapplications)
                        <span class="badge-pill badge-primary">{{count($pendingapplications)}}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Accepted
                    @if ($acceptedapplications)
                    <span class="badge-pill badge-success">{{count($acceptedapplications)}}</span>
                    @endif
                  </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Denied
                    @if ($deniedapplications)
                    <span class="badge-pill badge-danger">{{count($deniedapplications)}}</span>
                    @endif
                  </a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="list-group">
                        @if (count($pendingapplications) < 1)
                            <br/>
                            <p>No applications.</p>
                        @else
                            <br/>
                            <table id="dataTablePending" class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Submitted At</th>
                                    <th scope="col">View</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($pendingapplications as $application)
                                    <tr>
                                    <th scope="row">#{{$application->application_id}}</th>
                                    <td>{{$application->user->fullName('FLC')}}</td>
                                    <td>{{$application->submitted_at}}</td>
                                    <td>
                                        <a href="{{route('training.viewapplication', $application->application_id)}}"><i class="fa fa-eye"></i></a>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>



                  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                      <div class="list-group">
                          @if (count($acceptedapplications) < 1)
                              <br/>
                              <p>No applications.</p>
                          @else
                              <br/>
                              <table id="dataTableAccepted" class="table table-hover">
                                  <thead>
                                  <tr>
                                      <th scope="col">ID</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Submitted At</th>
                                      <th scope="col">View</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach ($acceptedapplications as $application)
                                      <tr>
                                      <th scope="row">#{{$application->application_id}}</th>
                                      <td>{{$application->user->fullName('FLC')}}</td>
                                      <td>{{$application->submitted_at}}</td>
                                      <td>
                                          <a href="{{route('training.viewapplication', $application->application_id)}}"><i class="fa fa-eye"></i></a>
                                      </td>
                                      </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          @endif
                      </div>
                  </div>

                  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                      <div class="list-group">
                          @if (count($deniedapplications) < 1)
                              <br/>
                              <p>No applications.</p>
                          @else
                              <br/>
                              <table id="dataTableDenied" class="table table-hover">
                                  <thead>
                                  <tr>
                                      <th scope="col">ID</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Submitted At</th>
                                      <th scope="col">View</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach ($deniedapplications as $application)
                                      <tr>
                                      <th scope="row">#{{$application->application_id}}</th>
                                      <td>{{$application->user->fullName('FLC')}}</td>
                                      <td>{{$application->submitted_at}}</td>
                                      <td>
                                          <a href="{{route('training.viewapplication', $application->application_id)}}"><i class="fa fa-eye"></i></a>
                                      </td>
                                      </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          @endif
                      </div>
                  </div>
                </div>
            <br>
            
@endif
    <script>
        $(document).ready(function() {
            $('#dataTablePending').DataTable( {
                "order": [[ 2, "desc" ]]
            } );
            $('#dataTableAccepted').DataTable( {
                "order": [[ 2, "desc" ]]
            } );
            $('#dataTableDenied').DataTable( {
                "order": [[ 2, "desc" ]]
            } );
        } );
    </script>
@stop
