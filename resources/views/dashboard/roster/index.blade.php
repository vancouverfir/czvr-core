@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'ATC Roster - Vancouver FIR')
@section('description', "Vancouver FIR's Controller Roster")

@section('content')

    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"; rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js";></script>
    </head>

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i
                class="fas fa-arrow-left"></i> Dashboard</a>
        <div class="container" style="margin-top: 20px;">
            <h1 class="blue-text font-weight-bold">Controller Roster</h1>
            <hr>
            <h3 class="font-weight-bold blue-text">Legend</h3>
        <hr>
        <div class="roster-legend">
            <table id="rosterLegend" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"><b>Qualification Tier</b></th>
                        <th style="text-align:center" scope="col"><b>Not Authorised</b></th>
                        <th style="text-align:center" scope="col"><b>With Supervision</b></th>
                        <th style="text-align:center" scope="col"><b>Solo Approved</b></th>
                        <th style="text-align:center" scope="col"><b>Certified</b></th>
                        </tr>
                </thead>
                 <tr>
                    <th scope="row">Unrestricted Positions</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></td>
                        <td class="text-center align-middle"><i class="far fa-user-circle icon-mentor"></td>
                        <td class="text-center align-middle"><i class="fa fa-minus-circle icon-solo"></td>
                        <td class="text-center align-middle"><i class="far fa-check-circle icon-certified"></td>
                </tr>
                <tr>
                    <th scope="row">Tier 2 Positions: CYVR and FSS</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></td>
                        <td class="text-center align-middle"><i class="fa fa-user-circle icon-mentor"></td>
                        <td class="text-center align-middle"><i class="fa fa-plus-circle icon-solo"></td>
                        <td class="text-center align-middle"><i class="fa fa-check-circle icon-certified"></td>
                </tr>
                </thead>
            </table>
        </div>
        <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                       aria-controls="home" aria-selected="true">Home Controllers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit"
                       aria-selected="false">Visiting Controllers</a>
                </li>
            </ul>
            <hr>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="font-weight-bold blue-text">Actions</h4>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="" data-target="#addController" data-toggle="modal"
                                       style="text-decoration:none;">
                        <span class="blue-text">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                                        &nbsp;
                                        <span class="text-colour">
                            Add a controller to roster
                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <!--Vancouver CONTROLLERS ROSTER-->
                    <table id="rosterVisitTable" class="table table-hover">
                        <thead>
                        <tr>
                            <th style="text-align:center" scope="col"><b>CID</b></th>
                            <th style="text-align:center" scope="col">Controller Name</th>
                            <th style="text-align:center" scope="col">Rating</th>
                            <th style="text-align:center" scope="col">FSS</th>
                            <th style="text-align:center" scope="col">DEL/GND</th>
                            <th style="text-align:center" scope="col">TWR</th>
                            <th style="text-align:center" scope="col">DEP</th>
                            <th style="text-align:center" scope="col">APP</th>
                            <th style="text-align:center" scope="col">CTR</th>
                            <th style="text-align:center" scope="col">Remarks</th>
                            <th style="text-align:center" scope="col">Status</th>
                            <th style="text-align:center" width="18%" class="text-danger" scope="col"><b>Actions</b>
                            </th>

                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($roster as $controller)
                            <tr>
                                <th scope="row"><b>{{$controller->cid}}</b></th>
                                <td align="center">
                                    {{$controller->user->fullName('FL')}}
                                </td>
                                <td align="center">
                                    {{$controller->user->rating_short}}
                                </td>

                                <!--Vancouver Controller Position Ratings from Db -->
                                 <!--AAS/RAAS-->
                                @if ($controller->fss == "0")
                                <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->fss == "1")
                                <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->fss == "2")
                                <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->fss == "3")
                                <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
                           
<!--Delivery/Ground-->
                            <td align="center">
                                    @if ($controller->delgnd == "0")
                                        <i class="fa fa-times-circle icon-no-cert"></i>
                                    @elseif ($controller->delgnd == "1")
                                        <i class="far fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->delgnd == "3")
                                        @if ($controller->delgnd_t2 != "3")
                                            <i class="far fa-check-circle icon-certified"></i>
                                        @endif
                                    @endif

                                    @if ($controller->delgnd_t2 == "1")
                                        <i class="fa fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->delgnd_t2 == "3")
                                        <i class="fa fa-check-circle icon-certified"></i>
@endif</td>
<!--Tower-->
                                <td align="center">
                                    @if ($controller->twr == "0")
                                        <i class="fa fa-times-circle icon-no-cert"></i>
                                    @elseif ($controller->twr == "1")
                                        <i class="far fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->twr == "2")
                                        <i class="fa fa-minus-circle icon-solo"></i>
                                    @elseif ($controller->twr == "3")
                                        @if ($controller->twr_t2 != "3")
                                            <i class="far fa-check-circle icon-certified"></i>
                                            @endif
                                    @endif

                                    @if ($controller->twr_t2 == "1")
                                        <i class="fa fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->twr_t2 == "2")
                                        <i class="fa fa-plus-circle icon-solo"></i>
                                    @elseif ($controller->twr_t2 == "3")
                                        <i class="fa fa-check-circle icon-certified"></i>
                                @endif</td>

<!--Departure-->
                                <td align="center">
                                    @if ($controller->dep == "0")
                                        <i class="fa fa-times-circle icon-no-cert"></i>
                                    @elseif ($controller->dep == "1")
                                        <i class="fa fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->dep == "2")
                                        <i class="fa fa-plus-circle icon-solo"></i>
                                    @elseif ($controller->dep == "3")
                                        <i class="fa fa-check-circle icon-certified"></i>
                                    @endif</td>
<!--Approach-->
                                <td align="center">
                                    @if ($controller->app == "0")
                                        <i class="fa fa-times-circle icon-no-cert"></i>
                                    @elseif ($controller->app == "1")
                                        <i class="far fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->app == "2")
                                        <i class="fa fa-minus-circle icon-solo"></i>
                                    @elseif ($controller->app == "3")
                                        @if ($controller->app_t2 != "3")
                                            <i class="far fa-check-circle icon-certified"></i>
                                            @endif
                                    @endif

                                    @if ($controller->app_t2 == "1")
                                        <i class="fa fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->app_t2 == "2")
                                        <i class="fa fa-plus-circle icon-solo"></i>
                                    @elseif ($controller->app_t2 == "3")
                                        <i class="fa fa-check-circle icon-certified"></i>
                                @endif</td>
<!--Centre-->
                                @if ($controller->ctr == "0")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->ctr == "1")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->ctr == "2")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->ctr == "3")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
                            <!--Remarks-->
                                <td align="center">
                                    {{$controller->remarks}}
                                </td>
                                <!--Active Status-->
                                @if ($controller->active == "0")
                                    <td align="center" class="bg-danger text-white">Not Active</td>
                                @elseif ($controller->active == "1")
                                    <td align="center" class="bg-success text-white">Active</td>
                                @else
                                    <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <!--Edit controller-->
                                <td align="center" style="width=100px">
                                    <a href="{{route('roster.editcontrollerform', [$controller->cid]) }}">
                                        <button class="btn btn-sm btn-info"
                                                style="vertical-align:top; float:left;">Edit
                                        </button>
                                    </a>

                                    </li>
                                    </ul>


                                    <!--END OF EDIT CONTROLLER-->
                                    <!--DELETE CONTROLLER-->
                                    <!--Confirm Delete controller button-->
                                    <div class="modal fade" id="deleteController{{$controller->id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm
                                                        Deletion</h5><br>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-weight:bold">Name: {{$controller->user->fullName('FL')}}</p>
                                                    <p style="font-weight:bold">CID: {{$controller->cid}}</p>
                                                    <h3 style="font-weight:bold; color:red">Are you sure you want to do this?</h3>
                                                    <p style="font-weight:bold">Deleting this member from the roster will delete their session logs and reset them back to the guest permissions.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET"
                                                          action="{{ route('roster.deletecontroller', [$controller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit" href="#">Delete
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal"
                                                            style="width:375px">Dismiss
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete controller-->
                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteController{{$controller->id}}"
                                       class="btn btn-sm btn-danger"
                                       style="vertical-align:bottom; float:right;">Delete</a>
                </div>
            </div>
            </td>
            </tr>

            @endforeach
            </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab">
            <div class="row">
                <div class="col-md-3">
                    <h4 class="font-weight-bold blue-text">Actions</h4>
                    <ul class="list-unstyled mt-2 mb-0">
                        <li class="mb-2">
                            <a href="" data-target="#addVisitController" data-toggle="modal"
                               style="text-decoration:none;">
                                <span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span
                                    class="text-colour">Add controller to roster</span></a>
                        </li>

                    </ul>
                </div>
                <!--Vancouver VISITING CONTROLLERS ROSTER-->
                <table id="rosterVisitTable" class="table table-hover">
                    <thead>

                    <tr>
                        <th style="text-align:center" scope="col"><b>CID</b></th>
                        <th style="text-align:center" scope="col">Controller Name</th>
                        <th style="text-align:center" scope="col">Rating</th>
                        <th style="text-align:center" scope="col">FSS</th>
                        <th style="text-align:center" scope="col">DEL/GND</th>
                        <th style="text-align:center" scope="col">TWR</th>
                        <th style="text-align:center" scope="col">DEP</th>
                        <th style="text-align:center" scope="col">APP</th>
                        <th style="text-align:center" scope="col">CTR</th>
                        <th style="text-align:center" scope="col">Remarks</th>
                        <th style="text-align:center" scope="col">Status</th>
                        <th style="text-align:center" width="18%" class="text-danger" scope="col"><b>Actions</b></th>

                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($visitroster2 as $visitcontroller)
                        <tr>
                            <th scope="row"><b>{{$visitcontroller->cid}}</b></th>
                            <td align="center">
                                {{$visitcontroller->user->fullName('FL')}}
                            </td>
                            <td align="center">
                                {{$visitcontroller->user->rating_short}}
                            </td>

                            <!--AAS/RAAS-->
                            @if ($visitcontroller->fss == "0")
                            <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                            @elseif ($visitcontroller->fss == "1")
                            <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                            @elseif ($visitcontroller->fss == "2")
                            <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                            @elseif ($visitcontroller->fss == "3")
                            <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                            @else
                            <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                            @endif

                            <!--Delivery/Ground-->
                            <td align="center">
                                @if ($visitcontroller->delgnd == "0")
                                    <i class="fa fa-times-circle icon-no-cert"></i>
                                @elseif ($visitcontroller->delgnd == "1")
                                    <i class="far fa-user-circle icon-mentor"></i>
                                @elseif ($visitcontroller->delgnd == "3")
                                    @if ($visitcontroller->delgnd_t2 != "3")
                                        <i class="far fa-check-circle icon-certified"></i>
                                        @endif
                                @endif

                                @if ($visitcontroller->delgnd_t2 == "1")
                                    <i class="fa fa-user-circle icon-mentor"></i>
                                @elseif ($visitcontroller->delgnd_t2 == "3")
                                    <i class="fa fa-check-circle icon-certified"></i>
                            @endif</td>
                            <!--Tower-->
                            <td align="center">
                            @if ($visitcontroller->twr == "0")
                                <i class="fa fa-times-circle icon-no-cert"></i>
                            @elseif ($visitcontroller->twr == "1")
                                <i class="far fa-user-circle icon-mentor"></i>
                            @elseif ($visitcontroller->twr == "2")
                                <i class="fa fa-minus-circle icon-solo"></i>
                            @elseif ($visitcontroller->twr == "3")
                                @if ($visitcontroller->twr_t2 != "3")
                                    <i class="far fa-check-circle icon-certified"></i>
                                    @endif
                            @endif

                            @if ($visitcontroller->twr_t2 == "1")
                                <i class="fa fa-user-circle icon-mentor"></i>
                            @elseif ($visitcontroller->twr_t2 == "2")
                                <i class="fa fa-plus-circle icon-solo"></i>
                            @elseif ($visitcontroller->twr_t2 == "3")
                                <i class="fa fa-check-circle icon-certified"></i>
                            @endif</td>

                            <!--Departure-->
                            <td align="center">
                            @if ($visitcontroller->dep == "0")
                                <i class="fa fa-times-circle icon-no-cert"></i>
                            @elseif ($visitcontroller->dep == "1")
                                <i class="fa fa-user-circle icon-mentor"></i>
                            @elseif ($visitcontroller->dep == "2")
                                <i class="fa fa-plus-circle icon-solo"></i>
                            @elseif ($visitcontroller->dep == "3")
                                <i class="fa fa-check-circle icon-certified"></i>
                            @endif</td>
                            <!--Approach-->
                            <td align="center">
                            @if ($visitcontroller->app == "0")
                                <i class="fa fa-times-circle icon-no-cert"></i>
                            @elseif ($visitcontroller->app == "1")
                                <i class="far fa-user-circle icon-mentor"></i>
                            @elseif ($visitcontroller->app == "2")
                                <i class="fa fa-minus-circle icon-solo"></i>
                            @elseif ($visitcontroller->app == "3")
                                @if ($visitcontroller->app_t2 != "3")
                                    <i class="far fa-check-circle icon-certified"></i>
                                    @endif
                            @endif

                            @if ($visitcontroller->app_t2 == "1")
                                <i class="fa fa-user-circle icon-mentor"></i>
                            @elseif ($visitcontroller->app_t2 == "2")
                                <i class="fa fa-plus-circle icon-solo"></i>
                            @elseif ($visitcontroller->app_t2 == "3")
                                <i class="fa fa-check-circle icon-certified"></i>
                            @endif</td>
                            <!--Centre-->
                            @if ($visitcontroller->ctr == "0")
                            <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                            @elseif ($visitcontroller->ctr == "1")
                            <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                            @elseif ($visitcontroller->ctr == "2")
                            <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                            @elseif ($visitcontroller->ctr == "3")
                            <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                            @else
                            <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                            @endif
                        <!--Remarks-->
                            <td align="center">
                                {{$visitcontroller->remarks}}
                            </td>
                            <!--Active Status-->
                            @if ($visitcontroller->active == "0")
                                <td align="center" class="bg-danger text-white">Not Active</td>
                            @elseif ($visitcontroller->active == "1")
                                <td align="center" class="bg-success text-white">Active</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                        @endif
                            <td align="center">
                                <a href="{{route('roster.editcontrollerform', [$visitcontroller->cid]) }}">
                                    <button class="btn btn-sm btn-info" style="vertical-align:top; float:left;">
                                        Edit
                                    </button>
                                </a>


                                <!--Delete controller-->
                                <!--Confirm Delete visitor button-->
                                <div class="row">
                                    <div class="modal fade" id="deleteVisitController{{$visitcontroller->id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm Deletion</h5>
                                                    <br>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-weight:bold">Name: {{$visitcontroller->user->fullName('FL')}}</p>
                                                    <p style="font-weight:bold">CID: {{$visitcontroller->cid}}</p>
                                                    <h3 style="font-weight:bold; color:red">Are you sure you want to do
                                                        this?</h3>
                                                    <p style="font-weight:bold">Deleting this member from the roster will
                                                        delete their session logs.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET"
                                                          action="{{ route('roster.deletecontroller', [$visitcontroller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit" href="#">Delete
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">
                                                        Dismiss
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete visitor-->

                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteVisitController{{$visitcontroller->id}}"
                                       class="btn btn-sm btn-danger" style="vertical-align:bottom; float:right;">Delete</a>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>

                <script>
                    $(document).ready(function () {
                        $('#rosterTable', '#rosterVisitTable').DataTable({
                            "order": [[0, "asc"]]
                        });
                    });
                </script>

            </div>
        </div>
    </div>

    <!--MODALS-->

    <!--Add Vancouver controller modal-->
    <div class="modal fade" id="addController" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Controller to Home Roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div align="center" class="modal-body pb-0">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('roster.addcontroller' )}}">
                                <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id}}">{{$user->id}}
                                            - {{$user->fname}} {{$user->lname}}</option>
                                    @endforeach
                                </select>
                                <br>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dropdown') }}</strong>
                                </span>
                                @endif
                                <td align="center">
                                    @csrf
                                    <br>
                                    <p class="font-weight-bold">This user will be added to the Home Roster.</p>
                                    <button type="submit" class="btn btn-success">Add User</button>
                                </td>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End add Vancouver controller modal-->

    <!--Add Visitor controller modal-->
    <div class="modal fade" id="addVisitController" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Controller to Visiting Roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div align="center" class="modal-body pb-0">

                    <div class="form-group row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('roster.addvisitcontroller' )}}">
                                <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id}}">{{$user->id}}
                                            - {{$user->fname}} {{$user->lname}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dropdown') }}</strong>
                                </span>
                                @endif
                                <br>
                                <br>

                                <td align="center">
                                    @csrf
                                    <br>
                                    <p class="font-weight-bold">This user will be added to the Visiting Roster.</p>
                                    <button type="submit" class="btn btn-success">Add User</button>
                                </td>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End add Visitor controller modal-->
</div>

@stop
