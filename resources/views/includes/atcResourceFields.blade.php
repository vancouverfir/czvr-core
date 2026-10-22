@php
    $currentIcon = old('font_awesome', $resource?->font_awesome ?? 'fa-cloud');
@endphp

<div class="resource-editor-grid">
    <div>
        <label for="{{ $fieldPrefix }}-title" class="form-label">Title</label>
        <input required class="form-control" type="text" name="title" id="{{ $fieldPrefix }}-title"
            value="{{ old('title', $resource?->title) }}" maxlength="120">
    </div>
    <div>
        <label for="{{ $fieldPrefix }}-icon" class="form-label">Icon</label>
        <div class="resource-icon-field">
            <span class="resource-icon-preview"><i id="{{ $fieldPrefix }}-icon-preview" class="fas {{ $currentIcon }}"></i></span>
            <select id="{{ $fieldPrefix }}-icon" name="font_awesome" class="form-select resource-icon-select"
                data-preview="{{ $fieldPrefix }}-icon-preview" required>
                @foreach ($resourceIcons as $icon => $label)
                    <option value="{{ $icon }}" @selected($currentIcon === $icon)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="mt-3">
    <label for="{{ $fieldPrefix }}-url" class="form-label">Resource URL</label>
    <input required class="form-control" type="url" name="url" id="{{ $fieldPrefix }}-url"
        value="{{ old('url', $resource?->url) }}" maxlength="2048" placeholder="https://">
</div>

<div class="mt-3">
    <label for="{{ $fieldPrefix }}-description" class="form-label">Description</label>
    <textarea required class="form-control" name="description" id="{{ $fieldPrefix }}-description" rows="4"
        maxlength="5000">{{ old('description', $resource?->description) }}</textarea>
    <small class="text-muted">A short summary shown on the resources page! Markdown is supported!</small>
</div>
