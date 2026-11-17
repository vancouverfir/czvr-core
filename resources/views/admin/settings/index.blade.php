@extends('layouts.master')

@push('styles')
    <link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">
@endpush

@section('content')
<main class="container py-4 settings-hub">
    <a href="{{ route('dashboard.index') }}" class="settings-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Dashboard</a>
    <div class="settings-heading">
        <h1 class="blue-text">Settings</h1>
    </div>

    @can('site-settings.manage')
        <section class="settings-section">
            <h2>Website</h2>
            <div class="settings-grid">
                <a class="settings-item" href="{{ route('settings.siteinformation') }}"><span class="settings-icon"><i class="fas fa-sliders-h"></i></span><span><strong>Site Information</strong><small>Site name, release, build and copyright.</small></span></a>
                <a class="settings-item" href="{{ route('settings.emails') }}"><span class="settings-icon"><i class="far fa-envelope"></i></span><span><strong>Staff Emails</strong><small>Email addresses for staff positions.</small></span></a>
                <a class="settings-item" href="{{ route('settings.banner') }}"><span class="settings-icon"><i class="far fa-flag"></i></span><span><strong>Site Banner</strong><small>The notice shown at the top of every page.</small></span></a>
                <a class="settings-item" href="{{ route('settings.images') }}"><span class="settings-icon"><i class="far fa-images"></i></span><span><strong>Homepage Images</strong><small>Photos on the homepage and who took them.</small></span></a>
            </div>
        </section>
    @endcan

    @if(auth()->user()->can('roles.manage') || auth()->user()->can('staff.manage'))
        <section class="settings-section">
            <h2>People & Access</h2>
            <div class="settings-grid">
                @can('roles.manage')
                    <a class="settings-item" href="{{ route('roles.view') }}"><span class="settings-icon"><i class="fas fa-user-shield"></i></span><span><strong>User Roles</strong><small>Roles and what each one can do.</small></span></a>
                @endcan
                @can('staff.manage')
                    <a class="settings-item" href="{{ route('settings.staff') }}"><span class="settings-icon"><i class="fas fa-users-cog"></i></span><span><strong>Staff Team</strong><small>Positions on the public staff page.</small></span></a>
                @endcan
            </div>
        </section>
    @endif

    @if(auth()->user()->can('network.manage') || auth()->user()->can('audit-log.access'))
        <section class="settings-section">
            <h2>Network & Logs</h2>
            <div class="settings-grid">
                @can('network.manage')
                    <a class="settings-item" href="{{ route('network.index') }}"><span class="settings-icon"><i class="fas fa-chart-line"></i></span><span><strong>Network Data</strong><small>Controller activity and session logs.</small></span></a>
                @endcan
                @can('audit-log.access')
                    <a class="settings-item" href="{{ route('settings.auditlog') }}"><span class="settings-icon"><i class="fas fa-history"></i></span><span><strong>Audit Log</strong><small>Changes made by staff.</small></span></a>
                @endcan
            </div>
        </section>
    @endif
</main>
@endsection
