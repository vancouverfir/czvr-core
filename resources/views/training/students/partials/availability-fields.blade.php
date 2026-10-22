@php
    $availabilityEditorId ??= 'studentAvailability';
    $availabilityRows = collect(old('availability', $student->availability ?: [
        ['days' => [], 'start' => '', 'end' => ''],
    ]))->map(function (array $period): array {
        $period['days'] = $period['days'] ?? (isset($period['day']) ? [$period['day']] : []);

        return $period;
    })->all();
    $weekdays = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];
@endphp

<div class="availability-editor" id="{{ $availabilityEditorId }}" data-availability-editor>
    <div class="availability-editor-heading">
        <div>
            <h4>Choose the days and times when you can normally train! All times are UTC!</h4>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-add-availability>
            <i class="fas fa-plus me-1" aria-hidden="true"></i>Add time
        </button>
    </div>

    @error('availability')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
    @enderror

    <div class="availability-rows" data-availability-rows>
        @foreach ($availabilityRows as $index => $slot)
            <div class="availability-row" data-availability-row>
                <fieldset class="availability-days">
                    <legend class="form-label">Days</legend>
                    <div class="availability-day-presets" aria-label="Quick day selections">
                        <span>Quick select:</span>
                        <button type="button" data-days-preset="weekdays">Mon–Fri</button>
                        <button type="button" data-days-preset="weekend">Weekend</button>
                        <button type="button" data-days-preset="all">Every day</button>
                    </div>
                    <div class="availability-day-options">
                        @foreach ($weekdays as $value => $label)
                            <input class="btn-check" type="checkbox" id="{{ $availabilityEditorId }}Day{{ $index }}{{ ucfirst($value) }}" name="availability[{{ $index }}][days][]" value="{{ $value }}" @checked(in_array($value, $slot['days'] ?? [], true))>
                            <label for="{{ $availabilityEditorId }}Day{{ $index }}{{ ucfirst($value) }}">{{ substr($label, 0, 3) }}</label>
                        @endforeach
                    </div>
                    @error("availability.{$index}.days")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error("availability.{$index}.days.*")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </fieldset>
                <div>
                    <label class="form-label" for="{{ $availabilityEditorId }}Start{{ $index }}">From UTC</label>
                    <input class="form-control" id="{{ $availabilityEditorId }}Start{{ $index }}" type="time" step="900" name="availability[{{ $index }}][start]" value="{{ $slot['start'] ?? '' }}" required>
                    @error("availability.{$index}.start")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label" for="{{ $availabilityEditorId }}End{{ $index }}">Until UTC</label>
                    <input class="form-control" id="{{ $availabilityEditorId }}End{{ $index }}" type="time" step="900" name="availability[{{ $index }}][end]" value="{{ $slot['end'] ?? '' }}" required>
                    @error("availability.{$index}.end")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <button type="button" class="availability-remove" data-remove-availability aria-label="Remove this period" title="Remove this period">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
        @endforeach
    </div>

    <template data-availability-template>
        <div class="availability-row" data-availability-row>
            <fieldset class="availability-days">
                <legend class="form-label">Days</legend>
                <div class="availability-day-presets" aria-label="Quick day selections">
                    <span>Quick select:</span>
                    <button type="button" data-days-preset="weekdays">Mon–Fri</button>
                    <button type="button" data-days-preset="weekend">Weekend</button>
                    <button type="button" data-days-preset="all">Every day</button>
                </div>
                <div class="availability-day-options">
                    @foreach ($weekdays as $value => $label)
                        <input class="btn-check" type="checkbox" id="{{ $availabilityEditorId }}Day__INDEX__{{ ucfirst($value) }}" name="availability[__INDEX__][days][]" value="{{ $value }}">
                        <label for="{{ $availabilityEditorId }}Day__INDEX__{{ ucfirst($value) }}">{{ substr($label, 0, 3) }}</label>
                    @endforeach
                </div>
            </fieldset>
            <div>
                <label class="form-label" for="{{ $availabilityEditorId }}Start__INDEX__">From UTC</label>
                <input class="form-control" id="{{ $availabilityEditorId }}Start__INDEX__" type="time" step="900" name="availability[__INDEX__][start]" required>
            </div>
            <div>
                <label class="form-label" for="{{ $availabilityEditorId }}End__INDEX__">Until UTC</label>
                <input class="form-control" id="{{ $availabilityEditorId }}End__INDEX__" type="time" step="900" name="availability[__INDEX__][end]" required>
            </div>
            <button type="button" class="availability-remove" data-remove-availability aria-label="Remove this period" title="Remove this period">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    </template>

    <div class="availability-comment">
        <label class="form-label" for="{{ $availabilityEditorId }}Comment">Comment <span class="text-muted">(optional)</span></label>
        <textarea class="form-control" id="{{ $availabilityEditorId }}Comment" name="availability_comment" rows="2" maxlength="1000" placeholder="Anything else our instructors should know!">{{ old('availability_comment', $student->times) }}</textarea>
        @error('availability_comment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
