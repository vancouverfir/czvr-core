@extends('layouts.master')
@section('title', 'Training Statistics - Vancouver FIR')

@push('styles')
    <link href="{{ asset('css/pages/training-instructor-dashboard.css') }}" rel="stylesheet">
    <style>
        .training-stats-body { padding: 1rem; }
        .stats-pane-grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(22rem, 1fr)); }
        .stats-summary { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); }
        .stats-summary-item strong { color: var(--czvr-green-dark); display: block; font-size: 2rem; font-weight: 500; line-height: 1.1; }
        .stats-summary-item span { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); }
        .stats-tabs .nav-link { color: var(--czvr-text-muted); }
        .stats-tabs .nav-link.active { color: var(--czvr-green-dark); }
        .stats-tiles { display: grid; gap: 0.75rem; grid-template-columns: repeat(auto-fit, minmax(9rem, 1fr)); }
        .stats-tile { background: var(--czvr-surface-muted); border-radius: 0.5rem; padding: 0.7rem 0.85rem; }
        .stats-tile strong { color: var(--czvr-text); display: block; font-size: 1.5rem; font-weight: 500; line-height: 1.2; }
        .stats-tile span { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); }
        .stats-subheading { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); margin: 0 0 0.5rem; text-transform: uppercase; }
        .stats-subheading:not(:first-child) { margin-top: 1rem; }
        .stats-bars { display: grid; gap: 0.45rem; }
        .stats-bar { align-items: center; display: grid; gap: 0.6rem; grid-template-columns: minmax(7rem, 11rem) 1fr 3.5rem; }
        .stats-bar-label { color: var(--czvr-text); font-size: var(--czvr-font-size-base); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .stats-bar-track { background: var(--czvr-surface-muted); border-radius: 4px; height: 0.9rem; overflow: hidden; }
        .stats-bar-fill { background: var(--czvr-green); border-radius: 0 4px 4px 0; height: 100%; min-width: 2px; }
        .stats-bar-fill.is-empty { min-width: 0; }
        .stats-bar-value { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); text-align: end; white-space: nowrap; }
        .stats-columns { align-items: end; display: grid; gap: 4px; grid-template-columns: repeat(12, 1fr); height: 11rem; }
        .stats-column { align-items: center; display: flex; flex-direction: column; height: 100%; justify-content: flex-end; }
        .stats-column-value { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); margin-bottom: 0.2rem; }
        .stats-column-fill { background: var(--czvr-green); border-radius: 4px 4px 0 0; min-height: 2px; width: 70%; }
        .stats-column-fill.is-empty { background: var(--czvr-border); }
        .stats-column-labels { display: grid; gap: 4px; grid-template-columns: repeat(12, 1fr); margin-top: 0.35rem; }
        .stats-column-labels span { color: var(--czvr-text-muted); font-size: var(--czvr-font-size-base); text-align: center; }
    </style>
@endpush

@php
    // Horizontal bar rows scaled to the largest value in the set
    $bars = function (array $values, string $unit) {
        $max = max([1, ...array_values($values)]);

        return collect($values)->map(fn ($value, $label) => [
            'label' => $label,
            'value' => $value,
            'width' => round($value / $max * 100, 1),
            'title' => $label.': '.$value.' '.Str::plural($unit, $value),
        ])->values();
    };
    $completionMax = max([1, ...array_values($completions)]);
@endphp

@section('content')
@include('includes.trainingMenu')

<main class="container training-page">
    <header class="training-page-header">
        <div>
            <h1 class="blue-text">Training Statistics</h1>
            <p>Waitlists, training progress, instructor workload, renewals and completions!</p>
        </div>
    </header>

    <div class="stats-summary mb-4">
        <div class="stats-summary-item"><strong>{{ array_sum(array_column($waitlists, 'count')) }}</strong><span>waiting</span></div>
        <div class="stats-summary-item"><strong>{{ array_sum($homePipeline) + array_sum($visitorPipeline) }}</strong><span>in training</span></div>
        <div class="stats-summary-item"><strong>{{ $consolidating->count() }}</strong><span>consolidating</span></div>
        <div class="stats-summary-item"><strong>{{ array_sum($completions) }}</strong><span>completed in 12 months</span></div>
    </div>

    <ul class="nav nav-tabs stats-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" id="stats-tab-waitlist" data-bs-toggle="tab" data-bs-target="#stats-waitlist" type="button" role="tab" aria-controls="stats-waitlist" aria-selected="true">Waitlist</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="stats-tab-pipeline" data-bs-toggle="tab" data-bs-target="#stats-pipeline" type="button" role="tab" aria-controls="stats-pipeline" aria-selected="false">Pipeline</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="stats-tab-instructors" data-bs-toggle="tab" data-bs-target="#stats-instructors" type="button" role="tab" aria-controls="stats-instructors" aria-selected="false">Instructors</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="stats-tab-renewals" data-bs-toggle="tab" data-bs-target="#stats-renewals" type="button" role="tab" aria-controls="stats-renewals" aria-selected="false">Renewals</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="stats-tab-completions" data-bs-toggle="tab" data-bs-target="#stats-completions" type="button" role="tab" aria-controls="stats-completions" aria-selected="false">Completions</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="stats-waitlist" role="tabpanel" aria-labelledby="stats-tab-waitlist" tabindex="0">
            <section class="training-panel" aria-labelledby="statsWaitlist">
                <div class="training-panel-header"><h2 id="statsWaitlist" class="blue-text">Waitlist</h2></div>
                <div class="training-stats-body">
                    @foreach ($waitlists as $name => $summary)
                        <h3 class="stats-subheading">{{ $name }}</h3>
                        <div class="stats-tiles">
                            <div class="stats-tile"><strong>{{ $summary['count'] }}</strong><span>waiting</span></div>
                            <div class="stats-tile"><strong>{{ $summary['average'] ?? '–' }}</strong><span>average days waiting</span></div>
                            <div class="stats-tile"><strong>{{ $summary['longest'] ?? '–' }}</strong><span>longest days waiting</span></div>
                            <div class="stats-tile"><strong>{{ $summary['availability'] === null ? '–' : $summary['availability'].'%' }}</strong><span>availability submitted</span></div>
                            <div class="stats-tile"><strong>{{ $summary['contacted'] === null ? '–' : $summary['contacted'].'%' }}</strong><span>marked contacted</span></div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="stats-pipeline" role="tabpanel" aria-labelledby="stats-tab-pipeline" tabindex="0">
            <div class="stats-pane-grid">
                <section class="training-panel" aria-labelledby="statsPipeline">
                    <div class="training-panel-header"><h2 id="statsPipeline" class="blue-text">Training Pipeline</h2><span class="training-panel-count">Active students per package</span></div>
                    <div class="training-stats-body">
                        @foreach (['Home students' => $homePipeline, 'Visitors' => $visitorPipeline] as $group => $pipeline)
                            <h3 class="stats-subheading">{{ $group }}</h3>
                            <div class="stats-bars">
                                @foreach ($bars($pipeline, 'student') as $bar)
                                    <div class="stats-bar" title="{{ $bar['title'] }}">
                                        <span class="stats-bar-label">{{ $bar['label'] }}</span>
                                        <span class="stats-bar-track"><span @class(['stats-bar-fill', 'd-block', 'is-empty' => $bar['value'] === 0]) style="width: {{ $bar['width'] }}%"></span></span>
                                        <span class="stats-bar-value">{{ $bar['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="training-panel" aria-labelledby="statsConsolidation">
                    <div class="training-panel-header"><h2 id="statsConsolidation" class="blue-text">Consolidation</h2><span class="training-panel-count">{{ $consolidating->count() }} consolidating</span></div>
                    <div class="training-stats-body">
                        @if ($consolidating->isEmpty())
                            <div class="training-empty">Nobody is consolidating right now!</div>
                        @else
                            <div class="stats-bars">
                                @foreach ($consolidating as $student)
                                    @php $hours = min($student->consolidationHours(), $consolidationHours); @endphp
                                    <div class="stats-bar" title="{{ $student->user->fullName('FL') }}: {{ number_format($student->consolidationHours(), 1) }} of {{ number_format($consolidationHours, 0) }} hours at {{ $student->consolidation_rating }}">
                                        <a href="{{ route('training.students.view', $student->id) }}" class="stats-bar-label">{{ $student->user->fullName('FL') }} ({{ $student->consolidation_rating }})</a>
                                        <span class="stats-bar-track"><span class="stats-bar-fill d-block" style="width: {{ round($hours / $consolidationHours * 100, 1) }}%"></span></span>
                                        <span class="stats-bar-value">{{ number_format($student->consolidationHours(), 1) }} / {{ number_format($consolidationHours, 0) }}h</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
        <div class="tab-pane fade" id="stats-instructors" role="tabpanel" aria-labelledby="stats-tab-instructors" tabindex="0">
            <section class="training-panel" aria-labelledby="statsWorkload">
                <div class="training-panel-header"><h2 id="statsWorkload" class="blue-text">Instructor Workload</h2><span class="training-panel-count">{{ $unassigned->count() }} unassigned {{ Str::plural('student', $unassigned->count()) }}</span></div>
                <div class="training-stats-body">
                    @if ($instructors->isEmpty())
                        <div class="training-empty">There are no active instructors or mentors!</div>
                    @else
                        <div class="stats-bars">
                            @php $workloadMax = max(1, $instructors->max('active_students_count')); @endphp
                            @foreach ($instructors as $instructor)
                                @php $name = $instructor->user?->fullName('FL') ?? 'Unknown'; @endphp
                                <div class="stats-bar" title="{{ $name }}: {{ $instructor->active_students_count }} active {{ Str::plural('student', $instructor->active_students_count) }}, {{ $instructor->upcoming_sessions_count }} upcoming {{ Str::plural('session', $instructor->upcoming_sessions_count) }}">
                                    <span class="stats-bar-label">{{ $name }}</span>
                                    <span class="stats-bar-track"><span @class(['stats-bar-fill', 'd-block', 'is-empty' => $instructor->active_students_count === 0]) style="width: {{ round($instructor->active_students_count / $workloadMax * 100, 1) }}%"></span></span>
                                    <span class="stats-bar-value">{{ $instructor->active_students_count }} · <i class="far fa-calendar" aria-hidden="true"></i> {{ $instructor->upcoming_sessions_count }}</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="small text-muted mt-3 mb-0">Bars show active students; <i class="far fa-calendar" aria-hidden="true"></i> is upcoming sessions.</p>
                    @endif
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="stats-renewals" role="tabpanel" aria-labelledby="stats-tab-renewals" tabindex="0">
            <section class="training-panel" aria-labelledby="statsRenewals">
                <div class="training-panel-header"><h2 id="statsRenewals" class="blue-text">Renewals</h2></div>
                <div class="training-stats-body">
                    <div class="stats-tiles">
                        @foreach ($renewals as $label => $count)
                            <div class="stats-tile"><strong>{{ $count }}</strong><span>{{ Str::lower($label) }}</span></div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="stats-completions" role="tabpanel" aria-labelledby="stats-tab-completions" tabindex="0">
            <section class="training-panel" aria-labelledby="statsCompletions">
                <div class="training-panel-header"><h2 id="statsCompletions" class="blue-text">Completions</h2><span class="training-panel-count">{{ array_sum($completions) }} in the last 12 months</span></div>
                <div class="training-stats-body">
                    <div class="stats-columns" role="img" aria-label="Training completions per month: {{ collect($completions)->map(fn ($count, $month) => $month.' '.$count)->join(', ') }}">
                        @foreach ($completions as $month => $count)
                            <div class="stats-column" title="{{ $month }}: {{ $count }} {{ Str::plural('completion', $count) }}">
                                @if ($count > 0)<span class="stats-column-value">{{ $count }}</span>@endif
                                <span @class(['stats-column-fill', 'is-empty' => $count === 0]) style="height: {{ $count > 0 ? round($count / $completionMax * 85, 1) : 0 }}%"></span>
                            </div>
                        @endforeach
                    </div>
                    <div class="stats-column-labels" aria-hidden="true">
                        @foreach (array_keys($completions) as $month)
                            <span>{{ \Illuminate\Support\Carbon::parse('1 '.$month)->format('M') }}</span>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@stop
