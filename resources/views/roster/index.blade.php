@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Roster - Vancouver FIR')
@section('description', "Vancouver FIR's Controller Roster")

@php
    $rosterTabs = [
        ['id' => 'home', 'label' => 'Home Controllers', 'table' => 'rosterTable', 'members' => $roster, 'addModal' => 'addController', 'rosterName' => 'home'],
        ['id' => 'visit', 'label' => 'Visiting Controllers', 'table' => 'visitRosterTable', 'members' => $visitroster, 'addModal' => 'addVisitController', 'rosterName' => 'visiting'],
    ];
@endphp

@section('content')
@include('partials.vendor.datatables')
<div class="container" style="margin-top: 20px;">
        <h1 class="blue-text fw-bold">Controller Roster</h1>
        <hr>
        <h3 class="fw-bold blue-text">Legend</h3>
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
                <tbody>
                    <tr>
                        <th scope="row">Unrestricted Positions</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></i></td>
                        <td class="text-center align-middle"><i class="far fa-user-circle icon-mentor"></i></td>
                        <td class="text-center align-middle"><i class="fa fa-minus-circle icon-solo"></i></td>
                        <td class="text-center align-middle"><i class="far fa-check-circle icon-certified"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">Tier 2 Positions: CYVR and FSS</th>
                        <td class="text-center align-middle"><i class="fa fa-times-circle icon-no-cert"></i></td>
                        <td class="text-center align-middle"><i class="fa fa-user-circle icon-mentor"></i></td>
                        <td class="text-center align-middle"><i class="fa fa-plus-circle icon-solo"></i></td>
                        <td class="text-center align-middle"><i class="fa fa-check-circle icon-certified"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach ($rosterTabs as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}-tab" data-bs-toggle="tab" href="#{{ $tab['id'] }}" role="tab" aria-controls="{{ $tab['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $tab['label'] }}</a>
            </li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            @foreach ($rosterTabs as $tab)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel" aria-labelledby="{{ $tab['id'] }}-tab"><br>

                @if ($canManage)
                <ul class="list-unstyled mb-3">
                    <li>
                        <a href="#" data-bs-target="#{{ $tab['addModal'] }}" data-bs-toggle="modal" style="text-decoration:none;">
                            <span class="blue-text"><i class="fas fa-chevron-right"></i></span>
                            &nbsp;
                            <span class="text-colour">Add a controller to the {{ $tab['rosterName'] }} roster</span>
                        </a>
                    </li>
                </ul>
                @endif

        <table id="{{ $tab['table'] }}" class="table table-hover">
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
                    @if ($canManage)
                    <th style="text-align:center" scope="col">Status</th>
                    <th style="text-align:center" class="text-danger" scope="col"><b>Actions</b></th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @foreach ($tab['members'] as $member)
                <tr>
                    <th style="text-align: center" scope="row"><a href="{{url('/roster/'.$member->cid)}}" class="blue-text"><b>{{$member->cid}}</b></a></th>
                    <td align="center">
                        {{$member->user->fullName('FL')}}
                    </td>
                    <td align="center">
                        {{$member->user->rating_short}}
                    </td>

                    @include('partials.roster-certifications', ['member' => $member])

                    <!--Remarks-->
                    <td align="center">
                        {{$member->remarks}}
                    </td>

                    @if ($canManage)
                    <!--Active Status-->
                    @if ($member->active == "0")
                        <td align="center" class="bg-danger text-white">Not Active</td>
                    @elseif ($member->active == "1")
                        <td align="center" class="bg-success text-white">Active</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif

                    <td align="center" class="align-middle text-nowrap">
                        <a href="{{ route('roster.editcontrollerform', [$member->cid]) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteController" data-name="{{ $member->user->fullName('FL') }}" data-cid="{{ $member->cid }}" data-action="{{ route('roster.deletecontroller', [$member->id]) }}" class="btn btn-sm btn-outline-danger">Delete</button>
                    </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
<br>
            </div>
            @endforeach
        </div>

    @if ($canManage)
    <div class="modal fade" id="deleteController" tabindex="-1" aria-labelledby="deleteControllerTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="#" id="deleteControllerForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteControllerTitle">Remove from Roster</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Are you sure you want to remove <strong data-delete-name></strong> (CID <span data-delete-cid></span>) from the roster?</p>
                        <p class="text-danger mb-0">This deletes their session logs and removes their Controller role!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ([['id' => 'addController', 'title' => 'Add Controller to Home Roster', 'route' => 'roster.addcontroller', 'roster' => 'Home'], ['id' => 'addVisitController', 'title' => 'Add Controller to Visiting Roster', 'route' => 'roster.addvisitcontroller', 'roster' => 'Visiting']] as $addModal)
    <div class="modal fade" id="{{ $addModal['id'] }}" tabindex="-1" role="dialog" aria-labelledby="{{ $addModal['id'] }}Title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $addModal['id'] }}Title">{{ $addModal['title'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div align="center" class="modal-body">
                    <form method="POST" action="{{ route($addModal['route']) }}">
                        @csrf
                        <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->fname }} {{ $user->lname }}</option>
                            @endforeach
                        </select>
                        <p class="fw-bold mt-3">This user will be added to the {{ $addModal['roster'] }} Roster.</p>
                        <button type="submit" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>
@stop

@push('scripts')
<script>
    $(function () {
        $('#rosterTable, #visitRosterTable').DataTable({
            order: [[0, 'asc']],
            @if ($canManage)
            columnDefs: [{ orderable: false, targets: [-1, -2] }],
            @endif
        });
    });

    @if ($canManage)
    document.getElementById('deleteController').addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('deleteControllerForm').action = button.dataset.action;
        event.target.querySelector('[data-delete-name]').textContent = button.dataset.name;
        event.target.querySelector('[data-delete-cid]').textContent = button.dataset.cid;
    });
    @endif
</script>
@endpush
