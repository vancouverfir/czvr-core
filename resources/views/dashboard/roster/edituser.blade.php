@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Edit User')
@section('description', "Vancouver FIR's Controller Roster")

@push('styles')
    <link href="{{ asset('css/pages/roster-edit.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $levels = [
        0 => 'Not Authorised',
        1 => 'With Supervision',
        2 => 'Solo Approved',
        3 => 'Certified',
    ];
    $icons = [
        'Unrestricted' => [0 => 'fa fa-times-circle icon-no-cert', 1 => 'far fa-user-circle icon-mentor', 2 => 'fa fa-minus-circle icon-solo', 3 => 'far fa-check-circle icon-certified'],
        'Tier 2' => [0 => 'fa fa-times-circle icon-no-cert', 1 => 'fa fa-user-circle icon-mentor', 2 => 'fa fa-plus-circle icon-solo', 3 => 'fa fa-check-circle icon-certified'],
    ];

    $positions = [
        'Delivery / Ground' => ['delgnd', 'delgnd_t2', false],
        'Tower' => ['twr', 'twr_t2', true],
        'Departure' => [null, 'dep', false],
        'Arrival' => ['app', 'app_t2', true],
        'Centre' => [null, 'ctr', true],
        'FSS' => [null, 'fss', true],
    ];
@endphp

<div class="container py-4">
    <a href="{{ route('roster.public') }}" class="blue-text" style="font-size: 1.2em;">
        <i class="fas fa-arrow-left"></i> Roster
    </a>

    <div class="card mt-3">
        <div class="card-header">
            <h1 class="blue-text h3 mb-1">Edit Controller Certifications</h1>
            <p class="mb-0 text-muted">{{ $roster->full_name }} · CID {{ $cid }}</p>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>The controller could not be updated!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('roster.editcontroller', $cid) }}">
                @csrf

                @php
                    $columns = collect($positions)->flatMap(fn ($position, $name) => collect(['Unrestricted' => $position[0], 'Tier 2' => $position[1]])
                        ->filter()
                        ->map(fn ($field, $tier) => ['position' => $name, 'tier' => $tier, 'field' => $field, 'soloAllowed' => $position[2]])
                        ->values());
                    $toggles = [
                        ['name' => 'active', 'label' => 'Roster status', 'current' => (string) (int) old('active', $roster->active), 'options' => ['1' => 'Active', '0' => 'Not Active']],
                        ['name' => 'rating_hours', 'label' => 'Reset rating hours', 'current' => old('rating_hours', 'false'), 'options' => ['false' => 'No', 'true' => 'Yes'], 'disabled' => ! $roster->canResetRatingHours()],
                    ];
                    $loggedMinutes = (int) round(max(0, (float) $roster->rating_hours) * 60);
                    $loggedTime = sprintf('%dh %02dm', intdiv($loggedMinutes, 60), $loggedMinutes % 60);
                @endphp
                <div class="alert roster-vatcan-note mb-3" role="note">
                    Endorsements and Solo Certifications must also be updated on VATCAN!
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-hover align-middle roster-cert-table">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2"></th>
                                @foreach ($columns->groupBy('position') as $position => $tiers)
                                    <th scope="colgroup" colspan="{{ $tiers->count() }}" class="text-center roster-cert-position"><span>{{ $position }}</span></th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($columns as $column)
                                    <th scope="col" class="text-center text-muted fw-normal small">{{ $column['tier'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($levels as $value => $label)
                                <tr>
                                    <th scope="row">{{ $label }}</th>
                                    @foreach ($columns as $column)
                                        @php($field = $column['field'])
                                        <td class="text-center">
                                            @if ($value !== 2 || $column['soloAllowed'])
                                                <input type="radio" class="btn-check" name="{{ $field }}" id="{{ $field }}-{{ $value }}" value="{{ $value }}" @checked((int) old($field, $roster->{$field}) === $value) aria-label="{{ $column['position'] }} {{ $column['tier'] }}: {{ $label }}" required>
                                                <label class="roster-cert-option" for="{{ $field }}-{{ $value }}" title="{{ $label }}">
                                                    <i class="{{ $icons[$column['tier']][$value] }}" aria-hidden="true"></i>
                                                </label>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label for="remarks" class="form-label fw-bold">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3" maxlength="2000" class="form-control border rounded px-2">{{ old('remarks', $roster->remarks) }}</textarea>
                </div>

                <div class="d-flex flex-wrap gap-4 mb-4 roster-edit-toggles">
                    @foreach ($toggles as $toggle)
                        <div>
                            <div class="fw-bold mb-1" id="{{ $toggle['name'] }}-label">{{ $toggle['label'] }}</div>
                            @php($disabled = $toggle['disabled'] ?? false)
                            @if ($disabled)
                                <input type="hidden" name="{{ $toggle['name'] }}" value="false">
                            @endif
                            <div class="btn-group" role="group" aria-labelledby="{{ $toggle['name'] }}-label">
                                @foreach ($toggle['options'] as $value => $text)
                                    <input type="radio" class="btn-check" name="{{ $toggle['name'] }}" id="{{ $toggle['name'] }}-{{ $value }}" value="{{ $value }}" @checked($disabled ? $value === 'false' : $toggle['current'] === (string) $value) @disabled($disabled) required>
                                    <label class="btn roster-toggle-option" for="{{ $toggle['name'] }}-{{ $value }}">{{ $text }}</label>
                                @endforeach
                            </div>
                            @if ($toggle['name'] === 'active' && (int) $roster->active === 1)
                                {{-- Only an active controller being deactivated is emailed (RosterController::editController). --}}
                                <div class="small text-danger mt-1 roster-inactive-warning">Saving will mark this controller inactive and email them!</div>
                            @endif
                            @if ($toggle['name'] === 'rating_hours' && $disabled)
                                <div class="small text-muted mt-1">This controller is not currently under consolidation!</div>
                            @elseif ($toggle['name'] === 'rating_hours')
                                <div class="small text-muted mt-1">
                                    {{ $loggedTime }} logged at the current rating (counts towards consolidation)!
                                </div>
                                <div class="small text-danger roster-reset-warning">Saving will reset these hours to 0!</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-success">
                    Save Controller
                </button>
            </form>
        </div>
    </div>
</div>
@stop
