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
                        <th scope="col"><b>Airports</b></th>
                        <th style="text-align:center" scope="col"><b>Not Authorised</b></th>
                        <th style="text-align:center" scope="col"><b>With Supervision</b></th>
                        <th style="text-align:center" scope="col"><b>Solo Approved</b></th>
                        <th style="text-align:center" scope="col"><b>Certified</b></th>
                        </tr>
                </thead>
                <tr>
                    <th scope="row">CYVR - Vancouver International</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></td>
                        <td class="text-center align-middle"><i class="fa fa-user-circle icon-mentor"></td>
                        <td class="text-center align-middle"><i class="fa fa-plus-circle icon-solo"></td>
                        <td class="text-center align-middle"><i class="fa fa-check-circle icon-certified"></td>
                </tr>
                <tr>
                    <th scope="row">Minor Airports</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></td>
                        <td class="text-center align-middle"><i class="far fa-user-circle icon-mentor"></td>
                        <td class="text-center align-middle"><i class="fa fa-minus-circle icon-solo"></td>
                        <td class="text-center align-middle"></td>
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
                    <th style="text-align:center" scope="col">DEL</th>
                    <th style="text-align:center" scope="col">GND</th>
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
                                @if ($controller->fss == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->fss == "2")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->fss == "3")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->fss == "4")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Delivery-->
                                @if ($controller->del == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->del == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->del == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($controller->del == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->del == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->del == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($controller->del == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center"><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Ground-->
                                @if ($controller->gnd == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->gnd == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->gnd == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($controller->gnd == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->gnd == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->gnd == "6")
                                   <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($controller->gnd == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Tower-->
                                @if ($controller->twr == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->twr == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->twr == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($controller->twr == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->twr == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->twr == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($controller->twr == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Departure-->
                                 @if ($controller->dep == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->dep == "2")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->dep == "3")
                                   <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->dep == "4")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Approach-->
                                @if ($controller->app == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->app == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->app == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($controller->app == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->app == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->app == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($controller->app == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Centre-->
                                @if ($controller->ctr == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($controller->ctr == "2")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($controller->ctr == "3")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($controller->ctr == "4")
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
                    <th style="text-align:center" scope="col">DEL</th>
                    <th style="text-align:center" scope="col">GND</th>
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
                                @if ($visitcontroller->fss == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->fss == "2")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->fss == "3")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->fss == "4")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Delivery-->
                                @if ($visitcontroller->del == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->del == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->del == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->del == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->del == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->del == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($visitcontroller->del == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center"><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Ground-->
                                @if ($visitcontroller->gnd == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->gnd == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->gnd == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->gnd == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->gnd == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->gnd == "6")
                                   <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($visitcontroller->gnd == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Tower-->
                                @if ($visitcontroller->twr == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->twr == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->twr == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->twr == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->twr == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->twr == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($visitcontroller->twr == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Departure-->
                                 @if ($visitcontroller->dep == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->dep == "2")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->dep == "3")
                                   <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->dep == "4")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Approach-->
                                @if ($visitcontroller->app == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->app == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->app == "3")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->app == "4")
                                    <td align="center" ><i class="fa fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->app == "5")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->app == "6")
                                    <td align="center" ><i class="fa fa-check-circle icon-certified"></i></td>
                                @elseif ($visitcontroller->app == "7")
                                    <td align="center" ><i class="fa fa-minus-circle icon-solo"></i> <i class="fa fa-user-circle icon-mentor"></i></td>
                                @else
                                    <td align="center" ><i class="fa fa-exclamation-triangle icon-error"></i></td>
                                @endif
<!--Centre-->
                                @if ($visitcontroller->ctr == "1")
                                    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
                                @elseif ($visitcontroller->ctr == "2")
                                    <td align="center" ><i class="far fa-user-circle icon-mentor"></i></td>
                                @elseif ($visitcontroller->ctr == "3")
                                    <td align="center" ><i class="fa fa-plus-circle icon-solo"></i></td>
                                @elseif ($visitcontroller->ctr == "4")
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
