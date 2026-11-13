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
                <a class="settings-item" href="{{ route('settings.siteinformation') }}"><span class="settings-icon"><i class="fas fa-sliders-h"></i></span><span><strong>Site information</strong><small>Name, release, build, and copyright details.</small></span></a>
                <a class="settings-item" href="{{ route('settings.emails') }}"><span class="settings-icon"><i class="far fa-envelope"></i></span><span><strong>Staff emails</strong><small>Contact addresses used throughout the website.</small></span></a>
                <a class="settings-item" href="{{ route('settings.banner') }}"><span class="settings-icon"><i class="far fa-flag"></i></span><span><strong>Site banner</strong><small>Publish or hide a site-wide notice.</small></span></a>
                <a class="settings-item" href="{{ route('settings.images') }}"><span class="settings-icon"><i class="far fa-images"></i></span><span><strong>Homepage images</strong><small>Upload, credit, and manage hero photography.</small></span></a>
            </div>
        </section>
    @endcan

    @if(auth()->user()->can('roles.manage') || auth()->user()->can('staff.manage'))
        <section class="settings-section">
            <h2>People & access</h2>
            <div class="settings-grid">
                @can('roles.manage')
                    <a class="settings-item" href="{{ route('roles.view') }}"><span class="settings-icon"><i class="fas fa-user-shield"></i></span><span><strong>User roles</strong><small>Build roles and choose their permissions.</small></span></a>
                @endcan
                @can('staff.manage')
                    <a class="settings-item" href="{{ route('settings.staff') }}"><span class="settings-icon"><i class="fas fa-users-cog"></i></span><span><strong>Staff team</strong><small>Maintain public staff positions and assignments.</small></span></a>
                @endcan
            </div>
        </section>
    @endif

    @if(auth()->user()->can('network.manage') || auth()->user()->can('audit-log.access'))
        <section class="settings-section">
            <h2>Operations</h2>
            <div class="settings-grid">
                @can('network.manage')
                    <a class="settings-item" href="{{ route('network.index') }}"><span class="settings-icon"><i class="fas fa-chart-line"></i></span><span><strong>Network data</strong><small>Controller activity, position sessions, and statistics.</small></span></a>
                @endcan
                @can('audit-log.access')
                    <a class="settings-item" href="{{ route('settings.auditlog') }}"><span class="settings-icon"><i class="fas fa-history"></i></span><span><strong>Audit log</strong><small>Review administrative changes made in Core.</small></span></a>
                @endcan
            </div>
        </section>
    @endif
</main>
@endsection
