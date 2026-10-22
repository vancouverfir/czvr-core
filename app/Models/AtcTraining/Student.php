<?php

namespace App\Models\AtcTraining;

use App\Console\Commands\RenewNotification;
use App\Models\Users\User;
use App\Notifications\CreateStudent;
use App\Services\TrainingProgressionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    public const STATUS_WAITLIST = 0;

    public const STATUS_ACTIVE = 1;

    public const STATUS_VISITOR_WAITLIST = 3;

    public const STATUS_MARKED_FOR_REMOVAL = 4;

    public const STATUS_VISITOR_ACTIVE = 5;

    public const STATUS_COMPLETED = 9;

    public const WAITLIST_STATUSES = [self::STATUS_WAITLIST, self::STATUS_VISITOR_WAITLIST];

    public const VISITOR_STATUSES = [self::STATUS_VISITOR_WAITLIST, self::STATUS_VISITOR_ACTIVE];

    public const RECERTIFICATION_LABEL = 'Recertification';

    public const RATING_TRAINING_LABELS = ['S1 Training', 'S2 Training', 'S3 Training', 'C1 Training'];

    public const MANAGED_TRAINING_LABELS = [...self::RATING_TRAINING_LABELS, self::RECERTIFICATION_LABEL];

    private const AVAILABILITY_DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    protected $fillable = [
        'user_id', 'availability', 'times', 'position', 'status', 'instructor_id', 'renewal_token', 'renewed_at', 'renewal_notified_at', 'renewal_reminded_at',
        'consolidation_rating',
    ];

    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'renewed_at' => 'datetime',
            'renewal_notified_at' => 'datetime',
            'renewal_reminded_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::creating(function (Student $student): void {
            $student->renewed_at ??= now();
        });

        // Leaving the waitlist cancels any open renewal
        static::updating(function (Student $student): void {
            if ($student->isDirty('status') && ! $student->isWaitlisted()) {
                $student->renewal_token = null;
                $student->renewal_notified_at = null;
                $student->renewal_reminded_at = null;
            }
        });

        static::created(function (Student $student): void {
            if (filled($student->user?->email)) {
                $student->user->notify(new CreateStudent($student));
            } else {
                Log::warning('New student notification skipped! Missing User or Email!!!', ['student_id' => $student->id]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function labels(): HasMany
    {
        return $this->hasMany(StudentInteractiveLabels::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(StudentChecklistItem::class);
    }

    public function instructingSessions(): HasMany
    {
        return $this->hasMany(InstructingSession::class);
    }

    public function trainingNotes(): HasMany
    {
        return $this->hasMany(StudentNote::class)->orderBy('created_at', 'desc');
    }

    public function latestWaitlistContactEvent(): HasOne
    {
        return $this->hasOne(StudentNote::class)->ofMany(
            ['id' => 'max'],
            fn ($query) => $query->whereIn('title', ['Waitlist Contacted', 'Waitlist Contact Cleared']),
        );
    }

    public function isWaitlisted(): bool
    {
        return in_array((int) $this->status, self::WAITLIST_STATUSES, true);
    }

    public function isVisitor(): bool
    {
        return in_array((int) $this->status, self::VISITOR_STATUSES, true);
    }

    /** The position after the last student in that queue */
    public static function nextQueuePosition(int $status): int
    {
        return ((int) self::where('status', $status)->max('position')) + 1;
    }

    /** Links a label (model or exact name) once; returns false when a named label does not exist */
    public function addLabel(StudentLabel|string $label): bool
    {
        $labelId = is_string($label) ? StudentLabel::where('name', $label)->value('id') : $label->id;

        if (! $labelId) {
            return false;
        }

        StudentInteractiveLabels::firstOrCreate(['student_id' => $this->id, 'student_label_id' => $labelId]);

        return true;
    }

    /** Ends training: removes every label and checklist item and sets status 9 */
    public function markTrainingCompleted(): void
    {
        $this->labels()->delete();
        $this->checklistItems()->delete();
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /** Training package names in checklist-map order */
    public static function trainingOrder(bool $visitor = false): array
    {
        $mapClass = $visitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;

        return $mapClass::orderBy('id')->with('label')->get()->pluck('label.name')->unique()->values()->all();
    }

    /** The first package in the training order that the student currently holds */
    public function currentTrainingLabel(array $trainingOrder): ?string
    {
        $labelNames = $this->labels->pluck('label.name')->all();

        return collect($trainingOrder)->first(fn ($label) => in_array($label, $labelNames, true));
    }

    /** The package after the student's current one, or null when there is none */
    public function nextTrainingLabel(array $trainingOrder): ?string
    {
        $current = $this->currentTrainingLabel($trainingOrder);

        return $current === null ? null : ($trainingOrder[array_search($current, $trainingOrder, true) + 1] ?? null);
    }

    public function isRecertification(): bool
    {
        if ($this->relationLoaded('labels')) {
            return $this->labels->contains(fn (StudentInteractiveLabels $link): bool => $link->label?->name === self::RECERTIFICATION_LABEL);
        }

        return $this->labels()->whereHas('label', fn ($query) => $query->where('name', self::RECERTIFICATION_LABEL))->exists();
    }

    /**
     * Every rating package up to the one held: S1 → S1 Training, S2 → up to S2 Training … C1 and above → all four.
     *
     * @return list<string>
     */
    public static function recertificationPackagesForRating(int $ratingId): array
    {
        return array_slice(self::RATING_TRAINING_LABELS, 0, max(0, min($ratingId, 5) - 1));
    }

    public function startsWithRecertification(): bool
    {
        return (int) $this->user?->rating_id >= 3;
    }

    public function consolidationIsPending(): bool
    {
        return $this->consolidation_rating !== null;
    }

    public function consolidationHours(): float
    {
        $this->loadMissing('user.rosterProfile');

        return max(0, (float) ($this->user?->rosterProfile?->rating_hours ?? 0));
    }

    public function consolidationRemainingHours(): float
    {
        return max(0, TrainingProgressionService::CONSOLIDATION_HOURS - $this->consolidationHours());
    }

    public function isConsolidating(): bool
    {
        return $this->consolidationIsPending() && $this->consolidationRemainingHours() > 0;
    }

    public function renewalDeadlineAt(): ?Carbon
    {
        return $this->renewal_notified_at?->copy()->addDays(RenewNotification::RENEWAL_GRACE_DAYS);
    }

    public function hasPendingRenewal(): bool
    {
        return $this->isWaitlisted()
            && filled($this->renewal_token)
            && (bool) $this->renewalDeadlineAt()?->isFuture();
    }

    public function renewalWindowExpired(): bool
    {
        return $this->isWaitlisted()
            && $this->renewal_notified_at !== null
            && ! $this->renewalDeadlineAt()->isFuture();
    }

    public function renewalReminderIsDue(): bool
    {
        return $this->hasPendingRenewal()
            && $this->renewal_reminded_at === null
            && now()->gte($this->renewalDeadlineAt()->subDays(RenewNotification::RENEWAL_REMINDER_DAYS));
    }

    public static function availabilityDaysLabel(array $days): string
    {
        $allDays = array_keys(self::AVAILABILITY_DAYS);
        $ordered = array_values(array_intersect($allDays, $days));

        $label = match ($ordered) {
            $allDays => 'Every day',
            array_slice($allDays, 0, 5) => 'Monday–Friday',
            ['saturday', 'sunday'] => 'Saturday–Sunday',
            default => null,
        };

        if ($label) {
            return $label;
        }

        // Two or more consecutive days read as a range, e.g. "Tuesday–Thursday"
        $positions = array_keys(array_intersect($allDays, $days));
        if (count($positions) > 1 && $positions === range($positions[0], end($positions))) {
            return self::AVAILABILITY_DAYS[$ordered[0]].'–'.self::AVAILABILITY_DAYS[end($ordered)];
        }

        return collect($ordered)->map(fn (string $day): string => self::AVAILABILITY_DAYS[$day])->join(', ', ' & ');
    }

    public function availabilityPeriods(): array
    {
        return collect($this->availability ?? [])
            ->map(fn (array $period): array => [
                'days' => self::availabilityDaysLabel($period['days'] ?? (isset($period['day']) ? [$period['day']] : [])),
                'start' => (string) ($period['start'] ?? ''),
                'end' => (string) ($period['end'] ?? ''),
            ])
            ->values()
            ->all();
    }
}
