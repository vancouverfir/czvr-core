@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@push('styles')
    <link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text fw-bold mt-2">Staff</h1>
        <hr>
            <button class="btn btn-primary" href="" data-bs-toggle="modal" data-bs-target="#addStaff">Add Staff Member</button>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addGroup">Add New Group</button>
        <div class="row">
            @foreach ($groups as $g)
        <div class="container">
            <hr>
            <h3 class="blue-text fw-bold mt-2">
                {{$g->name}}
                <button type="button" class="btn btn-sm btn-primary ms-2 align-middle text-decoration-none" data-bs-toggle="modal" data-bs-target="#editGroup{{$g->id}}" aria-label="Edit {{$g->name}}">Edit</button>
            </h3>
            @if ($g->description)
                <p class="text-muted mb-2">{{$g->description}}</p>
            @endif
            @if ($g->members->isEmpty())
                <p class="text-muted mb-3">No members yet!</p>
            @endif
        </div>
            @foreach ($g->members as $s)
            <div class="col-md-4 mb-3">
                <div class="card staff-card h-100">
                    <div class="card-header staff-card-header">
                        <span>{{$s->position}}</span>
                        <span class="staff-card-number">{{$s->shortform}}</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="staff-card-name">
                                    @if ($s->user->id == 1)
                                        <span class="text-muted">Vacant</span>
                                    @else
                                        {{$s->user->fullName('FLC')}}
                                    @endif
                                </h5>
                                {{-- These fields belong to the Save Changes form at the bottom of the page --}}
                                <input type="hidden" form="staffForm" name="members[{{$s->id}}][position]" value="{{$s->position}}">
                                <input type="hidden" form="staffForm" name="members[{{$s->id}}][shortform]" value="{{$s->shortform}}">
                                <input type="hidden" form="staffForm" name="members[{{$s->id}}][group_id]" value="{{$s->group_id}}">

                                <label>User ID (CID)</label>
                                <input required type="text" form="staffForm" name="members[{{$s->id}}][cid]" class="form-control form-control-sm" value="{{$s->user_id}}">
                            </div>
                            <div class="col-auto">
                                <img src="{{$s->user->avatar()}}" class="staff-card-avatar" alt="">
                            </div>
                        </div>
                        <label class="mt-3">Description</label>
                        <textarea class="form-control form-control-sm" rows="2" form="staffForm" name="members[{{$s->id}}][description]">{{$s->description}}</textarea>
                        <label class="mt-2">Email</label>
                        <input type="email" class="form-control form-control-sm" form="staffForm" name="members[{{$s->id}}][email]" value="{{$s->email}}">

                        <div class="row mt-auto pt-3">
                            <div class="col-lg-4">
                              <form method="POST" action="{{route('settings.staff.save')}}" id="delete" data-confirm="Delete {{$s->position}} ({{$s->shortform}})?">
                                    @csrf
                                    <input type="hidden" name="members[{{$s->id}}][cid]" value="{{$s->user_id}}">
                                    <input type="hidden" name="members[{{$s->id}}][position]" value="{{$s->position}}">
                                    <input type="hidden" name="members[{{$s->id}}][shortform]" value="{{$s->shortform}}">
                                    <input type="hidden" name="members[{{$s->id}}][group_id]" value="{{$s->group_id}}">
                                    <input type="hidden" name="members[{{$s->id}}][remove]" value="1">
                                    <button class="btn btn-sm btn-danger" type="submit" href="#">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
        </div>
        <hr>
        <form method="POST" action="{{route('settings.staff.save')}}" id="staffForm" class="mb-4">
            @csrf
            <button class="btn btn-success" type="submit">Save Changes</button>
        </form>
    </div>
    <!--Add Staff modal-->
    <div class="modal fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Staff Member</h5>
                </div>

                  <div class="modal-body">
                  <p>To set a position to vacant, set it to user ID 1.</p>
                        <div class="form-group row">

                            <div class="col-md-12">
                                <form method="POST" action="{{ route('settings.staff.save')}}">
                                  <label class="form-label mb-1 mt-2">Select User</label>
                                <select class="form-select" name="new_members[0][cid]">
                                  @foreach($users as $user)
                                  <option value="{{ $user->id}}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('dropdown') }}</strong>
                                    </span>
                                @endif
                                <label class="form-label mb-1 mt-2">Position Title</label>
                                <input type="text" class="form-control" name="new_members[0][position]">
                                <label class="form-label mb-1 mt-2">Staff Number (ZVR_)</label>
                                <input type="text" class="form-control" name="new_members[0][shortform]">
                                <label class="form-label mb-1 mt-2">Select a group</label>
                                <select class="form-select form-group" name="new_members[0][group]">
                                  @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                  @endforeach
                                </select>

                                <div class="text-center">
                                      @csrf
                                      <button type="submit" class="btn btn-success">Add Staff</button>
                                </div>
                                </form>
                            </div>
                        </div>

                </div>

                <div align="center" class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!--End add Staff modal-->

    <!--Add Group modal-->
    <div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-labelledby="addGroupTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupTitle">Add New Group</h5>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('settings.staff.save')}}">
                            <label class="form-label mb-1 mt-2">Group Name</label>
                            <input type="text" class="form-control" name="new_groups[0][name]" required maxlength="255">
                            <label class="form-label mb-1 mt-2">Description</label>
                            <input type="text" class="form-control" name="new_groups[0][description]" maxlength="2000">
                            <div class="form-check text-start my-2">
                                <input type="hidden" name="new_groups[0][can_receive_tickets]" value="0">
                                <input type="checkbox" class="form-check-input" id="newGroupTickets" name="new_groups[0][can_receive_tickets]" value="1">
                                <label class="form-check-label" for="newGroupTickets">Can receive tickets</label>
                            </div>

                            <div class="text-center">
                                @csrf
                                <button type="submit" class="btn btn-success">Add Group</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div align="center" class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!--End add Group modal-->

    <!--Edit Group modals-->
    @foreach ($groups as $g)
    <div class="modal fade" id="editGroup{{$g->id}}" tabindex="-1" role="dialog" aria-labelledby="editGroupTitle{{$g->id}}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupTitle{{$g->id}}">Edit Group</h5>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('settings.staff.save')}}" id="editGroupForm{{$g->id}}">
                            @csrf
                            <label class="form-label mb-1 mt-2">Group Name</label>
                            <input type="text" class="form-control" name="groups[{{$g->id}}][name]" value="{{$g->name}}" required maxlength="255">
                            <label class="form-label mb-1 mt-2">Description</label>
                            <input type="text" class="form-control" name="groups[{{$g->id}}][description]" value="{{$g->description}}" maxlength="2000">
                            <div class="form-check text-start my-2">
                                <input type="hidden" name="groups[{{$g->id}}][can_receive_tickets]" value="0">
                                <input type="checkbox" class="form-check-input" id="editGroupTickets{{$g->id}}" name="groups[{{$g->id}}][can_receive_tickets]" value="1" @checked($g->can_receive_tickets)>
                                <label class="form-check-label" for="editGroupTickets{{$g->id}}">Can receive tickets</label>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div align="center" class="modal-footer">
                    <button type="submit" form="editGroupForm{{$g->id}}" class="btn btn-success">Save Group</button>
                    @php($memberCount = $g->members->count())
                    <form method="POST" action="{{ route('settings.staff.save')}}" data-confirm="{{ $memberCount > 0 ? "Delete the {$g->name} group? Its {$memberCount} ".\Illuminate\Support\Str::plural('member', $memberCount).' will be deleted too! This cannot be undone!' : "Delete the {$g->name} group?" }}">
                        @csrf
                        <input type="hidden" name="groups[{{$g->id}}][name]" value="{{$g->name}}">
                        <input type="hidden" name="groups[{{$g->id}}][remove]" value="1">
                        <input type="hidden" name="groups[{{$g->id}}][delete_members]" value="1">
                        <button type="submit" class="btn btn-danger">Delete Group</button>
                    </form>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <!--End edit Group modals-->
@stop

@push('scripts')
<script>
(() => {
    let unsaved = false;

    document.querySelectorAll('[form="staffForm"]').forEach((field) => {
        field.addEventListener('input', () => { unsaved = true; });
    });

    document.addEventListener('submit', (event) => {
        const message = event.target.dataset.confirm;
        if (message && !confirm(message)) {
            event.preventDefault();
            return;
        }
        if (event.target.id === 'staffForm') {
            unsaved = false;
        }
    });

    window.addEventListener('beforeunload', (event) => {
        if (unsaved) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
})();
</script>
@endpush
