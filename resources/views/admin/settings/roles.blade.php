@extends('layouts.master')

@push('styles')
    <link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/permission-dependencies.js') }}"></script>
@endpush

@section('content')

    <main class="container py-4">
        <a href="{{ route('settings.index') }}" class="blue-text" style="font-size: 1.2em;"><i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text fw-bold mt-2">Roles &amp; Permissions</h1>
        <hr>

        <div class="row">
            <div class="col-md-4">
                @can('roles.manage')
                    <button type="button" class="btn btn-sm btn-success m-0 mb-3" data-bs-toggle="modal" data-bs-target="#newRole"><i class="fas fa-plus me-1"></i>New Role</button>
                @endcan
                <div class="card p-3 mb-4">
                    @forelse ($roles as $role)
                        <a href="{{ route('roles.view', ['role' => $role->id]) }}"
                            class="d-block py-2 {{ $loop->last ? '' : 'border-bottom' }} {{ $selectedRole?->is($role) ? 'blue-text fw-bold' : 'text-body' }}"
                            style="text-decoration: none;">{{ $role->name }}</a>
                    @empty
                        <span class="text-muted">No roles yet!</span>
                    @endforelse
                </div>
            </div>

            <div class="col-md-8">
                @if ($selectedRole)
                    @php($isAdministratorRole = in_array($selectedRole->slug, ['admin', 'administrator'], true))
                    @php($isSystemRole = in_array($selectedRole->slug, \App\Support\Authorization\SystemRoleCatalog::protectedSlugs(), true))
                    @php($canEdit = ! $isAdministratorRole && auth()->user()->can('roles.manage') && auth()->user()->canAssignRole($selectedRole))
                    @php($enabled = $isAdministratorRole
                        ? collect(\App\Support\Authorization\PermissionCatalog::slugs())->flip()
                        : $selectedRole->permissions->pluck('slug')->flip())

                    <h2 class="fw-bold blue-text pb-2">{{ $selectedRole->name }}</h2>
                    <div class="card p-3 mb-4">
                        @if ($isAdministratorRole)
                            <p class="mb-2">Administrators have every permission.</p>
                        @endif
                        <form method="POST" action="{{ route('roles.update', $selectedRole) }}" id="roleForm">
                            @csrf
                            @method('PUT')
                            @foreach ($permissionGroups as $group => $permissions)
                                <h5 class="{{ $loop->first ? '' : 'mt-3' }}">{{ $group }}</h5>
                                <div class="role-permission-grid">
                                    @foreach ($permissions as $slug => $permission)
                                        @include('partials.permission-checkbox', [
                                            'id' => 'role-'.$selectedRole->id.'-'.Str::slug($slug),
                                            'checked' => $enabled->has($slug),
                                            'disabled' => ! $canEdit || ! auth()->user()->canGrantPermission($slug),
                                        ])
                                    @endforeach
                                </div>
                            @endforeach
                        </form>
                        @can('roles.manage')
                            <div class="d-flex gap-2 mt-3">
                                @if ($canEdit)
                                    <button type="submit" form="roleForm" class="btn btn-sm btn-success ms-0">Save Role</button>
                                @endif
                                @unless ($isSystemRole)
                                    <form method="POST" action="{{ route('roles.delete', $selectedRole->id) }}"
                                        onsubmit="return confirm('Delete the {{ addslashes($selectedRole->name) }} role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete Role</button>
                                    </form>
                                @endunless
                            </div>
                        @endcan
                    </div>
                @else
                    <p class="text-muted">Create a role to begin assigning permissions.</p>
                @endif
            </div>
        </div>
    </main>

    @can('roles.manage')
        <div class="modal fade" id="newRole" tabindex="-1" aria-labelledby="newRoleTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form method="POST" action="{{ route('roles.add') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="newRoleTitle">New Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="roleName" class="form-label">Role name</label>
                            <input class="form-control" id="roleName" name="name" value="{{ old('name') }}" required maxlength="100">
                            <div class="mt-3">
                                <span>Starting permissions</span>
                                @foreach ($permissionGroups as $group => $permissions)
                                    <details class="role-permission-group" @if ($loop->first) open @endif>
                                        <summary>
                                            <span>{{ $group }}</span>
                                        </summary>
                                        <div class="role-permission-grid">
                                            @foreach ($permissions as $slug => $permission)
                                                @include('partials.permission-checkbox', [
                                                    'id' => 'new-role-'.Str::slug($slug),
                                                    'checked' => false,
                                                    'disabled' => ! auth()->user()->canGrantPermission($slug),
                                                ])
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-sm btn-success" type="submit">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
