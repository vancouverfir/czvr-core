@php($requires = $permission['requires'] ?? [])
<div class="form-check">
    <input type="checkbox" class="form-check-input" id="{{ $id }}" name="permissions[]" value="{{ $slug }}"
        @if ($requires) data-requires="{{ json_encode($requires) }}" @endif
        @checked($checked) @disabled($disabled)>
    <label class="form-check-label permission-label" data-bs-toggle="tooltip" title="{{ $permission['description'] }}" for="{{ $id }}">{{ $permission['label'] }}</label>
    @if ($requires)
        <small class="permission-needs">(needs {{ collect($requires)->map(fn ($required) => \App\Support\Authorization\PermissionCatalog::all()[$required]['label'])->join(', ', ' and ') }})</small>
    @endif
</div>
