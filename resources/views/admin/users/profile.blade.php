@extends('layouts.master')

@push('styles')
    <link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">
@endpush

@php
    $statusBadges = [
        'home' => ['text-bg-info', 'fa-user-check', 'CZVR Controller'],
        'visit' => ['text-bg-info', 'fa-plane', 'CZVR Visiting Controller'],
        'instructor' => ['text-bg-info', 'fa-chalkboard-teacher', 'CZVR Instructor'],
    ];
    [$statusColour, $statusIcon, $statusLabel] = $certification === null
        ? ['badge-muted', 'fa-user', 'Not a CZVR Controller']
        : ($statusBadges[$certification] ?? ['text-bg-dark', 'fa-question', 'Unknown']);
    $roster = $user->rosterProfile;
@endphp

@section('content')
    <div class="container py-4">
        <a href="{{ route('users.viewall') }}" class="blue-text" style="font-size: 1.2em;"><i class="fas fa-arrow-left"></i> Users</a>
        <h1 class="blue-text fw-bold mt-2"><img src="{{ $user->avatar() }}" style="height: 50px; width: 50px; margin-right: 15px; margin-bottom: 3px; border-radius: 50%;">{{ $user->fullName('FL') }}</h1>
        <hr>
        @if ($user->fname != $user->display_fname || ! $user->display_last_name || $user->display_cid_only)
            <p>Note: This user's display name does not match their CERT name!</p>
        @endif
        @if ($user->isSystemAccount())
            <div class="alert bg-czqo-blue-light">This account is a system account used to identify automatic actions, or to serve as a placeholder user!</div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <h2 class="fw-bold blue-text pb-2">Basic Data</h2>
                <div class="card p-3">
                    <h5>Identity</h5>
                    <ul class="list-unstyled">
                        <li>CID: {{ $user->id }}</li>
                        @can('users.manage')
                            <li>CERT First Name: {{ $user->fname }}</li>
                            <li>CERT Last Name: {{ $user->lname }}</li>
                        @endcan
                        <li>Display Name: {{ $user->fullName('FLC') }}</li>
                        <li>Rating: {{ $user->rating_GRP }} ({{ $user->rating_short }})</li>
                    </ul>
                    <h5>Email</h5>
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>

                    <h5 class="card-title mt-2">Status</h5>
                    <div class="d-flex">
                        <h3><span class="badge {{ $statusColour }} rounded shadow-none"><i class="fa {{ $statusIcon }}"></i>&nbsp; {{ $statusLabel }}</span></h3>
                        @if ($active !== null)
                            <h3><span class="badge ms-2 text-bg-{{ $active ? 'success' : 'danger' }} rounded shadow-none"><i class="fa {{ $active ? 'fa-check' : 'fa-times' }}"></i>&nbsp; {{ $active ? 'Active' : 'Inactive' }}</span></h3>
                        @endif
                    </div>

                    @if ($roster)
                        @php
                            $requiredHours = in_array($roster->status, ['home', 'visit', 'instructor'], true) ? config('currency.'.$roster->status, 3) : null;
                        @endphp
                        <h5 class="card-title mt-2">Activity</h5>
                        @if ($roster->currency < 0.1)
                            <h3><span class="badge rounded shadow-none red">No hours recorded</span></h3>
                        @endif
                        @if ($requiredHours !== null)
                            @if ($roster->currency != 0)
                                <h3><span class="badge rounded shadow-none {{ $roster->currency >= $requiredHours ? 'green' : 'purple' }}">{{ decimal_to_hm($roster->currency) }} recorded</span></h3>
                            @endif
                            <p>They require <b>{{ $requiredHours }} hours</b> of activity every quarter!</p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="fw-bold blue-text pb-2">Avatar</h2>
                <div class="card p-3 mb-4">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->avatar() }}" style="height: 100px; width: 100px; border-radius: 50%;">
                        <div class="ms-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#changeAvatar" class="btn btn-sm btn-primary">Change</a>
                            @unless ($user->isAvatarDefault())
                                <form action="{{ route('users.resetusersavatar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="submit" class="btn btn-sm bg-czqo-blue-light" value="Reset">
                                </form>
                            @endunless
                            <p class="mt-2 ps-1">Avatar Mode: {{ [0 => 'Default', 1 => 'Custom Image', 2 => 'Discord Avatar'][$user->avatar_mode] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <h2 class="fw-bold blue-text pb-2">Discord Info</h2>
                <div class="card p-3">
                    @if ($user->hasDiscord())
                        <h5><img style="border-radius: 50%; height: 30px;" class="img-fluid" src="{{ $user->getDiscordAvatar() }}" alt="">&nbsp;&nbsp;{{ $user->getDiscordUser()->username }}</h5>
                        <p class="d-flex align-items-center mb-0">Member of the Vancouver Discord: <i style="margin-left: 5px; font-size: 20px;" class="{{ $user->memberOfCZVRGuild() ? 'fas fa-check-circle green-text' : 'fas fa-times-circle red-text' }}"></i></p>
                    @else
                        This user does not have a linked Discord account!
                    @endif
                </div>
            </div>
        </div>

        @can('roles.manage')
            @php
                $assignedRoleIds = $user->roles->modelKeys();
                $directPermissionSlugs = $user->directPermissions->pluck('slug')->all();
            @endphp
            <h2 class="fw-bold blue-text pb-2 mt-4 d-flex align-items-center gap-2">
                Access
                @if ($directPermissionSlugs)
                    <span class="badge text-bg-info rounded shadow-none fs-6">Has individual permissions</span>
                @endif
            </h2>
            <div class="card p-3 mb-4">
                @if ($user->isSystemAccount())
                    <p class="mb-0 text-muted">This is a system account, so its roles and permissions can't be changed!</p>
                @else
                    <form method="POST" action="{{ route('users.access.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h3 class="h5 mb-0 blue-text">Roles</h3>
                                    <a href="{{ route('roles.view') }}" class="small">Edit roles</a>
                                </div>
                                @forelse ($allRoles as $role)
                                    <div class="py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="user-role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                                data-permissions="{{ json_encode(in_array($role->slug, ['admin', 'administrator'], true) ? \App\Support\Authorization\PermissionCatalog::slugs() : $role->permissions->pluck('slug')->all()) }}"
                                                @checked(in_array($role->id, $assignedRoleIds, true)) @disabled(! auth()->user()->canAssignRole($role))>
                                            <label class="form-check-label" for="user-role-{{ $role->id }}">{{ $role->name }}</label>
                                        </div>
                                        <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                    </div>
                                @empty
                                    <span class="text-muted">No roles available!</span>
                                @endforelse
                            </div>

                            <div class="col-md-8">
                                <details class="role-permission-group access-individual" @if ($directPermissionSlugs) open @endif>
                                    <summary>
                                        <span>Individual permissions @if ($directPermissionSlugs)<span class="access-individual-count">{{ count($directPermissionSlugs) }} enabled</span>@endif</span>
                                    </summary>
                                    @foreach ($permissionGroups as $group => $permissions)
                                        @php
                                            $enabledInGroup = collect($permissions)->keys()->intersect($directPermissionSlugs)->count();
                                        @endphp
                                        <details class="role-permission-group" @if ($enabledInGroup) open @endif>
                                            <summary>
                                                <span>{{ $group }}</span>
                                                <small>{{ $enabledInGroup }}/{{ count($permissions) }}</small>
                                            </summary>
                                            <div class="role-permission-grid">
                                                @foreach ($permissions as $slug => $permission)
                                                    @include('partials.permission-checkbox', [
                                                        'id' => 'user-permission-'.Str::slug($slug),
                                                        'checked' => in_array($slug, $directPermissionSlugs, true),
                                                        'disabled' => ! auth()->user()->canGrantPermission($slug),
                                                    ])
                                                @endforeach
                                            </div>
                                        </details>
                                    @endforeach
                                </details>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success mt-3">Save access</button>
                    </form>
                @endif
            </div>
        @endcan
    </div>

    <div class="modal fade" id="changeAvatar" tabindex="-1" aria-labelledby="changeAvatarTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('users.changeusersavatar') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeAvatarTitle">Change {{ $user->fullName('F') }}'s avatar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Abuse of this function will result in disciplinary action. This function should only be used for adjusting staff members' avatars for the staff page, or at a users request.</p>
                        <input type="file" class="form-control" name="file">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
                        <input type="submit" class="btn btn-success" value="Upload">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('js/permission-dependencies.js') }}"></script>
@endpush
