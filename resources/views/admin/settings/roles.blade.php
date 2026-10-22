@extends('layouts.master')

@section('content')
    <style>
        .role-editor-shell {
            border: 1px solid var(--czvr-border);
            border-radius: 0.7rem;
            overflow: hidden;
        }

        .role-editor-sidebar {
            background: var(--czvr-surface-muted);
            border-right: 1px solid var(--czvr-border);
            padding: 0.75rem;
        }

        .role-editor-link {
            align-items: center;
            border-radius: 0.45rem;
            color: var(--czvr-text);
            display: flex;
            justify-content: space-between;
            padding: 0.55rem 0.65rem;
            text-decoration: none;
        }

        .role-editor-link:hover,
        .role-editor-link.active {
            background: var(--czvr-surface);
            color: var(--czvr-green-dark);
        }

        .role-editor-link.active {
            box-shadow: inset 3px 0 var(--czvr-green);
        }

        .role-permission-group {
            border-top: 1px solid var(--czvr-border);
        }

        .role-permission-group > summary {
            align-items: center;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            list-style: none;
            padding: 0.7rem 0;
        }

        .role-permission-group > summary::-webkit-details-marker {
            display: none;
        }

        .role-permission-group > summary::after {
            content: "\f107";
            font-family: "Font Awesome 7 Free";
            font-weight: 900;
        }

        .role-permission-grid {
            display: grid;
            gap: 0.4rem 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding-bottom: 0.75rem;
        }

        .role-permission-grid .form-check {
            margin: 0;
            min-width: 0;
        }

        @media (max-width: 767.98px) {
            .role-editor-sidebar {
                border-bottom: 1px solid var(--czvr-border);
                border-right: 0;
            }

            .role-permission-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container py-4">
        <a href="{{ route('settings.index') }}"><i class="fas fa-arrow-left me-1"></i>Settings</a>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 mb-3">
            <div>
                <h1 class="blue-text mb-0">Roles &amp; permissions</h1>
                <span class="text-muted">Choose a role, then adjust its access.</span>
            </div>
            @can('roles.manage')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newRole">
                    <i class="fas fa-plus me-1"></i>New role
                </button>
            @endcan
        </div>

        <div class="role-editor-shell">
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

                <main class="col-12 col-md-9">
                    @if ($selectedRole)
                        @php($isAdministratorRole = in_array($selectedRole->slug, ['admin', 'administrator'], true))
                        @php($isSystemRole = in_array($selectedRole->slug, \App\Support\Authorization\SystemRoleCatalog::protectedSlugs(), true))
                        @php($enabled = $isAdministratorRole
                            ? collect(\App\Support\Authorization\PermissionCatalog::slugs())->flip()
                            : $selectedRole->permissions->pluck('slug')->flip())
                        <div class="p-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div>
                                    <h2 class="h4 mb-0">{{ $selectedRole->name }}</h2>
                                    <small class="text-muted">{{ $enabled->count() }} permissions enabled</small>
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
                                <p class="small text-muted mb-2">Administrator always grants every application permission.</p>
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
                                                <div class="form-check" title="{{ $permission['description'] }}">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="role-{{ $selectedRole->id }}-{{ Str::slug($slug) }}"
                                                        name="permissions[]" value="{{ $slug }}"
                                                        @checked($enabled->has($slug))
                                                        @disabled($isAdministratorRole || !auth()->user()->can('roles.manage') || !auth()->user()->canAssignRole($selectedRole) || !auth()->user()->canGrantPermission($slug))>
                                                    <label class="form-check-label"
                                                        for="role-{{ $selectedRole->id }}-{{ Str::slug($slug) }}">
                                                        {{ $permission['label'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach

                                @can('roles.manage')
                                    @unless ($isAdministratorRole || !auth()->user()->canAssignRole($selectedRole))
                                        <button type="submit" class="btn btn-sm btn-success mt-3">Save role</button>
                                    @endunless
                                @endcan
                            </form>
                        </div>
                    @else
                        <div class="p-4 text-muted">Create a role to begin assigning permissions.</div>
                    @endif
                </main>
            </div>
        </div>
    </div>

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
                                                <div class="form-check" title="{{ $permission['description'] }}">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="new-role-{{ Str::slug($slug) }}"
                                                        name="permissions[]" value="{{ $slug }}"
                                                        @disabled(!auth()->user()->canGrantPermission($slug))>
                                                    <label class="form-check-label" for="new-role-{{ Str::slug($slug) }}">
                                                        {{ $permission['label'] }}
                                                    </label>
                                                </div>
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
