@extends('layouts.master')
@push('styles')<link href="{{ asset('css/pages/network-data.css') }}" rel="stylesheet">@endpush
@section('content')
<main class="container py-4 network-page">
    <a class="network-back" href="{{ route('network.index', ['tab' => 'positions']) }}"><i class="fas fa-arrow-left"></i> Monitored Positions</a>
    <header class="network-heading"><h1 class="blue-text">{{ $position->identifier }}</h1><p>{{ $sessions->total() }} recorded {{ Str::plural('session', $sessions->total()) }}</p></header>
    <section class="network-panel mb-4"><div class="network-form-row"><form class="d-flex gap-2 flex-grow-1" action="{{ route('network.monitoredpositions.update', $position) }}" method="POST">@csrf @method('PUT')<label class="visually-hidden" for="identifier">Callsign</label><input id="identifier" class="form-control" name="identifier" value="{{ old('identifier', $position->identifier) }}" required><button class="btn btn-success">Save</button></form><form action="{{ route('network.monitoredpositions.delete', $position) }}" method="POST" onsubmit="return confirm('Remove this monitored position?')">@csrf @method('DELETE')<button class="btn btn-outline-danger" @disabled($position->sessions()->exists())>Delete</button></form></div>@if($position->sessions()->exists())<p class="network-form-note">Positions with session history cannot be deleted.</p>@endif</section>
    <section class="network-panel"><div class="network-panel-heading"><h2>Sessions</h2></div><div class="table-responsive"><table class="table network-table mb-0"><thead><tr><th>Controller</th><th>Callsign</th><th>Started</th><th>Ended</th><th class="text-end">Duration</th></tr></thead><tbody>
    @forelse($sessions as $session)<tr><td>{{ $session->user?->fullName('FL') ?? $session->cid }}<small>{{ $session->cid }}</small></td><td>{{ $session->callsign ?? $position->identifier }}</td><td>{{ $session->session_start->format('M j, Y H:i') }}</td><td>{{ $session->session_end?->format('M j, Y H:i') ?? 'Online now' }}</td><td class="text-end">{{ $session->duration === null ? '—' : decimal_to_hm($session->duration) }}</td></tr>@empty<tr><td colspan="5" class="network-empty">No sessions have been recorded for this position!</td></tr>@endforelse
    </tbody></table></div>@if($sessions->hasPages())<div class="p-3">{{ $sessions->links() }}</div>@endif</section>
</main>
@endsection
