@extends('layouts.master')
@section('title', 'Network Data - Vancouver FIR')
@push('styles')<link href="{{ asset('css/pages/network-data.css') }}" rel="stylesheet">@endpush
@section('content')
<main class="container py-4 network-page">
    <a class="network-back" href="{{ route('settings.index') }}"><i class="fas fa-arrow-left"></i> Settings</a>
    <header class="network-heading">
        <h1 class="blue-text">Network Data</h1>
    </header>

    <section class="network-metrics" aria-label="Network summary">
        <div><strong>{{ $summary['online'] }}</strong><span>online now</span></div>
        <div><strong>{{ $summary['controllers'] }}</strong><span>active controllers</span></div>
        <div><strong>{{ $summary['below'] }}</strong><span>below activity requirement</span></div>
        <div><strong>{{ $summary['positions'] }}</strong><span>monitored positions</span></div>
    </section>

    <ul class="nav nav-tabs network-tabs mb-3" role="tablist">
        @foreach (['activity' => 'Activity', 'statistics' => 'Positions', 'positions' => 'Monitored Positions'] as $key => $label)
            <li class="nav-item" role="presentation">
                <button @class(['nav-link', 'active' => $tab === $key]) id="network-tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#network-{{ $key }}" type="button" role="tab" aria-controls="network-{{ $key }}" aria-selected="{{ $tab === $key ? 'true' : 'false' }}">{{ $label }}</button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        <div @class(['tab-pane', 'fade', 'show active' => $tab === 'activity']) id="network-activity" role="tabpanel" aria-labelledby="network-tab-activity" tabindex="0">
            <div class="network-filter" role="navigation" aria-label="Activity filter">
                @foreach (['all' => 'All', 'met' => 'Requirement met', 'below' => 'Below requirement'] as $value => $label)
                    <a @class(['active' => $filter === $value]) href="{{ route('network.index', array_filter(['tab' => 'activity', 'status' => $value === 'all' ? null : $value, 'period' => $period === '90' ? null : $period])) }}">{{ $label }}</a>
                @endforeach
            </div>
            <section class="network-panel">
                <div class="table-responsive"><table class="table network-table mb-0"><thead><tr><th>Controller</th><th>Status</th><th>Last session</th><th>Progress</th><th class="text-end">Hours this quarter</th></tr></thead><tbody>
                @forelse($members as $member)
                    @php($percentage = $member->required_hours > 0 ? min(100, ($member->recorded_hours / $member->required_hours) * 100) : 100)
                    <tr><td>{{ $member->user?->fullName('FL') ?? $member->full_name }}<small>{{ $member->cid }}</small></td><td>{{ ucfirst($member->status) }}</td><td>{{ $member->last_session_at ? \Carbon\Carbon::parse($member->last_session_at)->diffForHumans() : 'No session recorded' }}</td><td><div class="network-progress"><span style="width: {{ $percentage }}%"></span></div></td><td class="text-end">{{ decimal_to_hm($member->recorded_hours) }} / {{ decimal_to_hm($member->required_hours) }}</td></tr>
                @empty<tr><td colspan="5" class="network-empty">No controllers match this filter!</td></tr>@endforelse
                </tbody></table></div>
            </section>
        </div>

        <div @class(['tab-pane', 'fade', 'show active' => $tab === 'statistics']) id="network-statistics" role="tabpanel" aria-labelledby="network-tab-statistics" tabindex="0">
            <form method="GET" class="network-period">
                <input type="hidden" name="tab" value="statistics">
                <label for="period">Period</label>
                <select id="period" name="period" class="form-select form-select-sm no-custom-select" onchange="this.form.submit()">
                    <option value="30" @selected($period === '30')>Last 30 days</option>
                    <option value="90" @selected($period === '90')>Last 90 days</option>
                    <option value="365" @selected($period === '365')>Last year</option>
                    <option value="all" @selected($period === 'all')>All available{{ $availableFrom ? ' (since '.\Carbon\Carbon::parse($availableFrom)->format('M j, Y').')' : '' }}</option>
                </select>
                <span>{{ $statistics['sessions'] }} {{ Str::plural('session', $statistics['sessions']) }} · {{ number_format($statistics['hours'], 1) }} hours · {{ $statistics['controllers'] }} {{ Str::plural('controller', $statistics['controllers']) }} · average {{ decimal_to_hm($statistics['average']) }}</span>
            </form>
            <div class="row g-3">
                <div class="col-lg-6"><section class="network-panel h-100"><div class="network-panel-heading"><h2>Busiest positions</h2></div><div class="table-responsive"><table class="table network-table mb-0"><thead><tr><th>Position</th><th>Sessions</th><th class="text-end">Hours</th></tr></thead><tbody>@forelse($busiestPositions as $row)<tr><td>{{ $row->monitoredPosition?->identifier ?? 'Removed position' }}</td><td>{{ $row->session_count }}</td><td class="text-end">{{ decimal_to_hm((float) $row->total_hours) }}</td></tr>@empty<tr><td colspan="3" class="network-empty">No position activity for this period!</td></tr>@endforelse</tbody></table></div></section></div>
                <div class="col-lg-6"><section class="network-panel h-100"><div class="network-panel-heading"><h2>Most active controllers</h2></div><div class="table-responsive"><table class="table network-table mb-0"><thead><tr><th>Controller</th><th>Sessions</th><th class="text-end">Hours</th></tr></thead><tbody>@forelse($busiestControllers as $row)<tr><td>{{ $row->user?->fullName('FL') ?? $row->cid }}<small>{{ $row->cid }}</small></td><td>{{ $row->session_count }}</td><td class="text-end">{{ decimal_to_hm((float) $row->total_hours) }}</td></tr>@empty<tr><td colspan="3" class="network-empty">No controller activity for this period!</td></tr>@endforelse</tbody></table></div></section></div>
            </div>
        </div>

        <div @class(['tab-pane', 'fade', 'show active' => $tab === 'positions']) id="network-positions" role="tabpanel" aria-labelledby="network-tab-positions" tabindex="0">
            @if($errors->createMonitoredPosition->any())<div class="alert alert-danger">{{ $errors->createMonitoredPosition->first() }}</div>@endif
            <section class="network-panel">
                <div class="network-panel-heading">
                    <h2>{{ $positions->count() }} monitored {{ Str::plural('position', $positions->count()) }}</h2>
                    <div class="d-flex gap-3 align-items-center">
                        <a href="{{ route('network.monitoredpositions.download') }}"><i class="fas fa-download me-1"></i>Download JSON</a>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createPositionModal"><i class="fas fa-plus me-1"></i>Add position</button>
                    </div>
                </div>
                <div class="table-responsive"><table class="table network-table mb-0"><thead><tr><th>Position</th><th>Sessions</th><th>Last online</th><th></th></tr></thead><tbody>
                @forelse($positions as $position)<tr><td>{{ $position->identifier }}</td><td>{{ $position->sessions_count }}</td><td>{{ $position->lastOnlinePretty() }}</td><td class="text-end"><a href="{{ route('network.monitoredpositions.view', strtolower($position->identifier)) }}">Manage</a></td></tr>@empty<tr><td colspan="4" class="network-empty">No positions are currently monitored.</td></tr>@endforelse
                </tbody></table></div>
            </section>
        </div>
    </div>
</main>

<div class="modal fade" id="createPositionModal" tabindex="-1" aria-labelledby="createPositionTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form action="{{ route('network.monitoredpositions.create') }}" method="POST">@csrf<div class="modal-header"><h5 class="modal-title" id="createPositionTitle">Add monitored position</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><label class="form-label" for="identifier">Exact callsign</label><input id="identifier" name="identifier" value="{{ old('identifier') }}" class="form-control" placeholder="CYVR_TWR" required><div class="form-text">Letters, numbers, underscores, and asterisks are accepted.</div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Add position</button></div></form></div></div></div>
@if($errors->createMonitoredPosition->any())<script>document.addEventListener('DOMContentLoaded', () => bootstrap.Modal.getOrCreateInstance(document.getElementById('createPositionModal')).show());</script>@endif
@endsection
