@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <style>
        .user-access-role {
            border-bottom: 1px solid var(--czvr-border);
            padding: 0.55rem 0;
        }

        .user-access-role:last-child {
            border-bottom: 0;
        }

        .user-access-group {
            border-top: 1px solid var(--czvr-border);
        }

        .user-access-group > summary {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            list-style: none;
            padding: 0.65rem 0;
        }

        .user-access-group > summary::-webkit-details-marker {
            display: none;
        }

        .user-access-grid {
            display: grid;
            gap: 0.35rem 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding-bottom: 0.65rem;
        }

        @media (max-width: 575.98px) {
            .user-access-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container py-4">
        <a href="{{route('users.viewall')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Users</a>
        <h1 class="blue-text fw-bold mt-2"><img src="{{$user->avatar()}}" style="height: 50px; width:50px;margin-right: 15px; margin-bottom: 3px; border-radius: 50%;">{{$user->fullName('FL')}}</h1>
        <hr>
        @if ($user->fname != $user->display_fname || !$user->display_last_name || $user->display_cid_only)
            <p>Note: This user's display name does not match their CERT name.</p>
        @endif
        @if($user->id == 1 || $user->id == 2)
        <div class="alert bg-czqo-blue-light">
            This account is a system account used to identify automatic actions, or to serve as a placeholder user!
        </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <h2 class="fw-bold blue-text pb-2">Basic Data</h2>
                <div class="card p-3">
                    <h5>Identity</h5>
                    <ul class="list-unstyled">
                        <li>CID: {{$user->id}}</li>
                        @can('users.manage')
                        <li>CERT First Name: {{$user->fname}}</li>
                        <li>CERT Last Name: {{$user->lname}}</li>
                        @endcan
                        <li>Display Name: {{$user->fullName('FLC')}}</li>
                        <li>Rating: {{$user->rating_GRP}} ({{$user->rating_short}})</li>

                    </ul>
                    <h5>Email</h5>
                    <a href="mailto:{{$user->email}}">{{$user->email}}</a>
                    <br>
                    <h5 class="card-title">Status</h5>
                    <div class="card-text">
                        <div class="d-flex flex-row justify-content-left">
                        @if ($certification == "certified")
                            <h3>
                            <span class="badge  text-bg-success rounded shadow-none">
                                <i class="fa fa-check"></i>&nbsp;
                                CZVR Certified
                            </span>
                            </h3>
                        @elseif ($certification == "not_certified")
                            <h3>
                            <span class="badge text-bg-danger rounded shadow-none">
                                <i class="fa fa-times"></i>&nbsp;
                                Not Certified to Control
                            </span>
                            </h3>
                        @elseif ($certification == "training")
                            <h3>
                            <span class="badge text-bg-warning rounded shadow-none">
                                <i class="fa fa-book-open"></i>&nbsp;
                                In Training
                            </span>
                            </h3>
                        @elseif ($certification == "home")
                            <h3>
                            <span class="badge text-bg-info rounded shadow-none">
                                <i class="fa fa-user-check"></i>&nbsp;
                                CZVR Controller
                            </span>
                            </h3>
                        @elseif ($certification == "visit")
                            <h3>
                            <span class="badge text-bg-info rounded shadow-none">
                                <i class="fa fa-plane"></i>&nbsp;
                                    CZVR Visiting Controller
                            </span>
                            </h3>
                        @elseif ($certification == "instructor")
                            <h3>
                            <span class="badge text-bg-info rounded shadow-none">
                                <i class="fa fa-chalkboard-teacher"></i>&nbsp;
                                        CZVR Instructor
                            </span>
                            </h3>
                        @else
                            <h3>
                            <span class="badge text-bg-dark rounded shadow-none">
                                <i class="fa fa-question"></i>&nbsp;
                                Unknown
                            </span>
                            </h3>
                        @endif
                        @if ($active == 0)
                            <h3>
                            <span class="badge ms-2 text-bg-danger rounded shadow-none">
                                <i class="fa fa-times"></i>&nbsp;
                                Inactive
                            </span>
                            </h3>
                        @elseif ($active == 1)
                            <h3>
                            <span class="badge ms-2 text-bg-success rounded shadow-none">
                                <i class="fa fa-check"></i>&nbsp;
                                Active
                            </span>
                            </h3>
                        @endif
                        </div>
                          <span class="text-danger">
                    </div>
                    <!--All users, no hours-->
                    @if ($user->rosterProfile)
                    <h5 class="card-title mt-2">Activity</h5>
                        @if ($user->rosterProfile->currency < 0.1)
                        <h3><span class="badge rounded shadow-none red">
                            No hours recorded
                        </span></h3>
                        @endif
<!--Vancouver Training Hrs-->
                        @if ($user->rosterProfile->status == "training")
                        @if (!$user->rosterProfile->currency == 0)
                          @if ($user->rosterProfile->currency < 3.0)
                          <h3><span class="badge rounded shadow-none purple">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @elseif ($user->rosterProfile->currency >= 3.0)
                          <h3><span class="badge rounded shadow-none green">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @endif
                          @endif
                              <p>They require <b>3 hours</b> of activity every quarter.</p>
                        @endif
<!--End Vancouver Training Hours-->

<!--Vancouver Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "home")
                        @if (!$user->rosterProfile->currency == 0)
                          @if ($user->rosterProfile->currency < 3.0)
                          <h3><span class="badge rounded shadow-none purple">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @elseif ($user->rosterProfile->currency >= 3.0)
                          <h3><span class="badge rounded shadow-none green">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @endif
                          @endif
                              <p>They require <b>3 hours</b> of activity every quarter.</p>
                        @endif
<!--End Vancouver Cntrlr Hours-->

<!--Vancouver Vstr Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "visit")
                        @if (!$user->rosterProfile->currency == 0)
                        @if ($user->rosterProfile->currency < 3.0)
                          <h3><span class="badge rounded shadow-none purple">
                              {{auth()->user()->rosterProfile->currency}} hours recorded
                          </span></h3>
                        @elseif ($user->rosterProfile->currency >= 3.0)
                          <h3><span class="badge rounded shadow-none green">
                              {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                        @endif
                        @endif
                        <p>They require <b>3 hours</b> of activity every quarter.</p>
                        @endif

<!--End Vancouver Cntrlr Hours-->

<!--Vancouver Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "instructor")
                        @if (!$user->rosterProfile->currency == 0)
                            @if ($user->rosterProfile->currency < 3.0)
                                <h3><span class="badge rounded shadow-none purple">
                                {{$user->rosterProfile->currency}} hours recorded
                            </span></h3>
                            @elseif ($user->rosterProfile->currency >= 3.0)
                                <h3><span class="badge rounded shadow-none green">
                                {{$user->rosterProfile->currency}} hours recorded
                                </span></h3>
                            @endif
                            @endif
                            <p>They require <b>3 hours</b> of activity every quarter.</p>
                        @endif
<!--End Vancouver Instrctr Hours-->

                    @endif

                </div>
            </div>
            <div class="col-md-6">
            <h2 class="fw-bold blue-text pb-2">Avatar</h2>
                <div class="card p-3">
                    <div class="d-flex flex-row align-items-center">
                        <img src="{{$user->avatar()}}" style="height: 100px; width: 100px; border-radius: 50%;">
                        <div class="ms-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#changeAvatar" class="btn btn-sm btn-primary">Change</a>
                            @if(!$user->isAvatarDefault())
                            <form action="{{route('users.resetusersavatar')}}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="submit" class="btn btn-sm bg-czqo-blue-light" value="Reset">
                            </form>
                            @endif
                            <p class="mt-2 ps-1">Avatar Mode:
                                @switch($user->avatar_mode)
                                @case(0)Default
                                @break
                                @case(1)Custom Image
                                @break
                                @case(2)Discord Avatar
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div><br>
                <h2 class="fw-bold blue-text pb-2">Discord Info</h2>
                <div class="card p-3">
                    @if($user->hasDiscord())
                    <h5><img style="border-radius:50%; height: 30px;" class="img-fluid" src="{{$user->getDiscordAvatar()}}" alt="">&nbsp;&nbsp;{{$user->getDiscordUser()->username}}</h5>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center">Member of the Vancouver Discord: <i style="margin-left: 5px;font-size: 20px;" class="{{$user->memberOfCZVRGuild() ? 'fas fa-check-circle green-text' : 'fas fa-times-circle red-text'}}"></i></li>
                    </ul>
                    @else
                    This user does not have a linked Discord account.
                    @endif
                </div>
            </div>
        </div>

        @can('roles.manage')
            @php
                $assignedRoleIds = $user->roles->modelKeys();
                $directPermissionSlugs = $user->directPermissions->pluck('slug')->all();
            @endphp
            <h2 class="fw-bold blue-text pb-2 mt-4">Access</h2>
            <div class="card p-3 mb-4">
                <form method="POST" action="{{ route('users.access.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h3 class="h5 mb-0">Roles</h3>
                                <a href="{{ route('roles.view') }}" class="small">Edit roles</a>
                            </div>
                            @forelse ($allRoles as $role)
                                <div class="user-access-role">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="user-role-{{ $role->id }}"
                                            name="roles[]" value="{{ $role->id }}"
                                            @checked(in_array($role->id, $assignedRoleIds, true))
                                            @disabled(! auth()->user()->canAssignRole($role))>
                                        <label class="form-check-label" for="user-role-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                    <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                </div>
                            @empty
                                <span class="text-muted">No roles available!</span>
                            @endforelse
                        </div>

                        <div class="col-md-8">
                            <details>
                                <summary class="mb-2">
                                    Individual permissions
                                    <small class="text-muted ms-1">({{ count($directPermissionSlugs) }} enabled)</small>
                                </summary>
                                <p class="small text-muted">Optional exceptions for this user! Normal access should come from roles!</p>
                                @foreach ($permissionGroups as $group => $permissions)
                                    <details class="user-access-group">
                                        <summary>
                                            <span>{{ $group }}</span>
                                            <small class="text-muted">{{ collect($permissions)->keys()->intersect($directPermissionSlugs)->count() }}/{{ count($permissions) }}</small>
                                        </summary>
                                        <div class="user-access-grid">
                                    @foreach ($permissions as $slug => $permission)
                                                <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="user-permission-{{ Str::slug($slug) }}" name="permissions[]" value="{{ $slug }}" @checked(in_array($slug, $directPermissionSlugs, true)) @disabled(! auth()->user()->canGrantPermission($slug))>
                                            <label class="form-check-label" for="user-permission-{{ Str::slug($slug) }}">{{ $permission['label'] }}</label>
                                        </div>
                                    @endforeach
                                        </div>
                                    </details>
                                @endforeach
                            </details>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success mt-3">Save access</button>
                </form>
            </div>
        @endcan
    </div>
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">User Note</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('users.createnote', $user->id)}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Confidential</label>
                        <input type="checkbox" name="confidential" id="confidential" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!--View Notes modal-->
    <div class="modal fade" id="viewNotesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View User Notes</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach($userNotes as $note)
                    <div class="list-group-item pe-0">
                        <div class="d-flex flex-row justify-content-between">
                        <ul class="list-unstyled">
                            <li><p>Author: {{$note->author_name}}</p></li>
                            <li><p>Date/Time: {{$note->timestamp}}</p></li>
                            <li><p>Notes: {{$note->toHtml('content')}}</p></li>
                            <form action="{{route('users.deletenote', [$user->id, $note->id])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger mt-1">Delete</button>
                            </form>
                        </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Change avatar modal-->
    <div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Change {{$user->fullName('F')}}'s avatar</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('users.changeusersavatar')}}" enctype="multipart/form-data" class="" id="">
                <div class="modal-body">
                    <p>Abuse of this function will result in disciplinary action. This function should only be used for adjusting staff members' avatars for the staff page, or at a users request.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="file">
                            <input type="hidden" name="user_id" value="{{$user->id}}">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                    <input type="submit" class="btn btn-success" value="Upload">
                </div>
                </form>
            </div>
        </div>
    </div>
    <!--End change avatar modal-->
    <script>
        function displayDeleteModal() {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        }
    </script>
@stop
