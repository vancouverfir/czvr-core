@extends('layouts.master')

@push('styles')
    <link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/permission-dependencies.js') }}"></script>
@endpush

@section('content')

    <main class="container py-4 settings-hub">
        <a href="{{ route('settings.index') }}" class="settings-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Settings</a>
        <div class="settings-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
            <div>
                <h1 class="blue-text">Roles &amp; permissions</h1>
                <p>Choose a role, then adjust its access. Hover a permission to see what it unlocks.</p>
            </div>
            @can('roles.manage')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newRole">
                    <i class="fas fa-plus me-1"></i>New role
                </button>
            @endcan
        </div>

        <div class="role-editor-shell settings-form">
            <div class="row g-0">
                <aside class="col-12 col-md-3 role-editor-sidebar">
                    @forelse ($roles as $role)
                        <a class="role-editor-link {{ $selectedRole?->is($role) ? 'active' : '' }}"
                            href="{{ route('roles.view', ['role' => $role->id]) }}">
                            <span>{{ $role->name }}</span>
                            <small class="text-muted">{{ in_array($role->slug, ['admin', 'administrator'], true) ? count(\App\Support\Authorization\PermissionCatalog::slugs()) : $role->permissions->count() }}</small>
                        </a>
                    @empty
                        <span class="text-muted">No roles yet.</span>
                    @endforelse
                </aside>

                <div class="col-12 col-md-9">
                    @if ($selectedRole)
                        @php($isAdministratorRole = in_array($selectedRole->slug, ['admin', 'administrator'], true))
                        @php($isSystemRole = in_array($selectedRole->slug, \App\Support\Authorization\SystemRoleCatalog::protectedSlugs(), true))
                        @php($enabled = $isAdministratorRole
                            ? collect(\App\Support\Authorization\PermissionCatalog::slugs())->flip()
                            : $selectedRole->permissions->pluck('slug')->flip())
                        <div class="role-editor-header">
                            <div>
                                <h2>{{ $selectedRole->name }}</h2>
                                <small>{{ $enabled->count() }} permissions enabled</small>
                            </div>
                            @can('roles.manage')
                                @unless ($isSystemRole)
                                    <form method="POST" action="{{ route('roles.delete', $selectedRole->id) }}"
                                        onsubmit="return confirm('Delete the {{ addslashes($selectedRole->name) }} role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endunless
                            @endcan
                        </div>

                        @if ($isAdministratorRole)
                            <p class="role-editor-note">Administrator always grants every application permission.</p>
                        @endif

                        <form method="POST" action="{{ route('roles.update', $selectedRole) }}">
                                @csrf
                                @method('PUT')
                                @foreach ($permissionGroups as $group => $permissions)
                                    @php($enabledInGroup = collect($permissions)->keys()->intersect($enabled->keys())->count())
                                    <details class="role-permission-group" @if ($loop->first) open @endif>
                                        <summary>
                                            <span>{{ $group }}</span>
                                            <small class="text-muted">{{ $enabledInGroup }}/{{ count($permissions) }}</small>
                                        </summary>
                                        <div class="role-permission-grid">
                                            @foreach ($permissions as $slug => $permission)
                                                @include('partials.permission-checkbox', [
                                                    'id' => 'role-'.$selectedRole->id.'-'.Str::slug($slug),
                                                    'checked' => $enabled->has($slug),
                                                    'disabled' => $isAdministratorRole || ! auth()->user()->can('roles.manage') || ! auth()->user()->canAssignRole($selectedRole) || ! auth()->user()->canGrantPermission($slug),
                                                ])
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach

                                @can('roles.manage')
                                    @unless ($isAdministratorRole || !auth()->user()->canAssignRole($selectedRole))
                                        <div class="settings-actions"><button type="submit" class="btn btn-success">Save role</button></div>
                                    @endunless
                                @endcan
                            </form>
                    @else
                        <div class="p-4 text-muted">Create a role to begin assigning permissions.</div>
                    @endif
                </div>
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
                            <h5 class="modal-title" id="newRoleTitle">New role</h5>
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
                                            <small class="text-muted">{{ count($permissions) }}</small>
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
                            <button class="btn btn-sm btn-success" type="submit">Create role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
