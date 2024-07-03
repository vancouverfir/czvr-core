@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Roster - Vancouver FIR')
@section('description', "Vancouver FIR's Controller Roster")

@section('content')
<div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">Controller Roster</h1>
        <hr>
        <h3 class="font-weight-bold blue-text">Legend</h3>
        <hr>
        <div class="roster-legend corner">
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
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home Controllers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit" aria-selected="false">Visiting Controllers</a>
            </li>
            @if (Auth::check() && Auth::user()->permissions >= 3)
            <li class="nav-item">
                <a class="nav-link" href="{{route('roster.index')}}" style="color:brown">Edit Roster</a>
            @endif
          </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"><br>

<!--Vancouver CONTROLLERS ROSTER-->
        <table id="rosterTable" class="table table-hover">
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
                </tr>
            </thead>
            <tbody>
            @foreach ($roster as $controller)
            @if($controller->active == "1")
                    <th style="text-align: center" scope="row"><a href="{{url('/roster/'.$controller->cid)}}" class="blue-text"><b>{{$controller->cid}}</b></a></th>
                    <td align="center" >
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
                                    @elseif ($controller->delgnd == "2")
                                        <i class="fa fa-minus-circle icon-solo"></i>
                                    @elseif ($controller->delgnd == "3")
                                        @if ($controller->delgnd_t2 != "3")
                                            <i class="far fa-check-circle icon-certified"></i>
                                            @endif
                                    @endif

                                    @if ($controller->delgnd_t2 == "1")
                                        <i class="fa fa-user-circle icon-mentor"></i>
                                    @elseif ($controller->delgnd_t2 == "2")
                                        <i class="fa fa-plus-circle icon-solo"></i>
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
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
<br>
</div>
<div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab"><br>

<!--Vancouver VISITING CONTROLLERS ROSTER-->
        <table id="visitRosterTable" class="table table-hover">
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
                </tr>
            </thead>
            <tbody>
            @foreach ($visitroster as $visitcontroller)
            @if($visitcontroller->active == "1")
                <tr>
                    <th style="text-align: center" scope="row"><a href="{{url('/roster/'.$visitcontroller->cid)}}" class="blue-text"><b>{{$visitcontroller->cid}}</b></a></th>
                    <td align="center" >
                        {{$visitcontroller->user->fullName('FL')}}
                    </td>
                    <td align="center">
                        {{$visitcontroller->user->rating_short}}
                    </td>

<!--Vancouver Controller Position Ratings from Db -->
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
                                @elseif ($visitcontroller->delgnd == "2")
                                    <i class="fa fa-minus-circle icon-solo"></i>
                                @elseif ($visitcontroller->delgnd == "3")
                                    @if ($visitcontroller->delgnd_t2 != "3")
                                        <i class="far fa-check-circle icon-certified"></i>
                                        @endif
                                @endif

                                @if ($visitcontroller->delgnd_t2 == "1")
                                    <i class="fa fa-user-circle icon-mentor"></i>
                                @elseif ($visitcontroller->delgnd_t2 == "2")
                                    <i class="fa fa-plus-circle icon-solo"></i>
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
                </tr>
                @endif
            @endforeach
            </tbody>
        </table><br>

        </div>
    </div>
</div>
<script>
        $(document).ready(function() {
            $.fn.dataTable.enum(['S1', 'S2', 'S3', 'C1', 'C3', 'I1', 'I3', 'SUP', 'ADM'])
            $('#rosterTable').DataTable( {
                "order": [[ 0, "asc" ]]
            } );
        } );
    </script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/enum.js"></script>
@stop
