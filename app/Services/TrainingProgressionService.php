<?php

namespace App\Services;

use App\Helpers\CreateNote;
use App\Models\AtcTraining\LabelChecklistMap;
use App\Models\AtcTraining\LabelChecklistVisitorMap;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentChecklistItem;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use App\Models\Users\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrainingProgressionService
{
    /** @var array<int, array{short: string, long: string}> */
    private const RATINGS = [
        -1 => ['short' => 'INA', 'long' => 'Inactive'],
        0 => ['short' => 'SUS', 'long' => 'Suspended'],
        1 => ['short' => 'OBS', 'long' => 'Observer'],
        2 => ['short' => 'S1', 'long' => 'Tower Trainee'],
        3 => ['short' => 'S2', 'long' => 'Tower Controller'],
        4 => ['short' => 'S3', 'long' => 'TMA Controller'],
        5 => ['short' => 'C1', 'long' => 'Enroute Controller'],
        6 => ['short' => 'C2', 'long' => 'Controller 2'],
        7 => ['short' => 'C3', 'long' => 'Senior Controller'],
        8 => ['short' => 'I1', 'long' => 'Instructor'],
        9 => ['short' => 'I2', 'long' => 'Instructor 2'],
        10 => ['short' => 'I3', 'long' => 'Senior Instructor'],
        11 => ['short' => 'SUP', 'long' => 'Supervisor'],
        12 => ['short' => 'ADM', 'long' => 'Administrator'],
    ];

    /** @var array<string, string> */
    private const NEXT_TRAINING_PACKAGE = [
        'S1' => 'S1 Training',
        'S2' => 'S3 Training',
        'S3' => 'C1 Training',
    ];

    /** @var list<string> */
    private const CONSOLIDATION_RATINGS = ['S2', 'S3'];

    /** Rating hours to log at a new S2/S3 rating before the next training package unlocks. */
    public const CONSOLIDATION_HOURS = 25.0;

    /**
     * Update a local rating and advance the training workflow when a future
     * promotion is detected. S2 and S3 start consolidation; S1 advances and
     * C1 completes training immediately. Existing ratings are the baseline.
     *
     * @param  array{short: string, long: string}|null  $definition
     */
    public function synchronizeRating(User $user, int $newRatingId, ?array $definition = null): bool
    {
        $definition ??= self::RATINGS[$newRatingId] ?? null;

        if (! $definition) {
            Log::warning('Training progression received an unknown VATSIM rating!', [
                'cid' => $user->id,
                'rating_id' => $newRatingId,
            ]);

            return false;
        }

        $oldRatingId = is_numeric($user->rating_id) ? (int) $user->rating_id : null;
        $ratingChanged = $oldRatingId !== $newRatingId;

        if (! $ratingChanged) {
            $roster = $user->rosterProfile()->first();
            if ($roster) {
                $this->completeConsolidationIfEligible($roster);
            }

            return false;
        }

        DB::transaction(function () use ($user, $newRatingId, $definition, $oldRatingId): void {
            $user->forceFill([
                'rating_id' => $newRatingId,
                'rating_short' => $definition['short'],
                'rating_long' => $definition['long'],
                'rating_GRP' => $definition['long'],
            ])->save();

            $roster = $user->rosterProfile()->lockForUpdate()->first();
            if ($roster) {
                $roster->update(['rating_hours' => 0]);
            }

            // Null/zero is an initial import, and a lower rating is not a promotion.
            if ($oldRatingId === null || $oldRatingId <= 0 || $newRatingId <= $oldRatingId) {
                return;
            }

            $student = Student::with(['labels.label', 'user.rosterProfile'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $student || in_array((int) $student->status, [Student::STATUS_VISITOR_WAITLIST, Student::STATUS_MARKED_FOR_REMOVAL, Student::STATUS_VISITOR_ACTIVE, Student::STATUS_COMPLETED], true)) {
                return;
            }

            if (! in_array($definition['short'], self::CONSOLIDATION_RATINGS, true)) {
                $this->unlockNextTrainingStage($student, $definition['short'], false);

                return;
            }

            $this->beginConsolidation($student, $definition['short']);
        });

        Log::info('User rating synchronized!', [
            'cid' => $user->id,
            'from' => $oldRatingId,
            'to' => $newRatingId,
        ]);

        return true;
    }

    public function completeConsolidationIfEligible(RosterMember $roster): bool
    {
        return DB::transaction(function () use ($roster): bool {
            $student = Student::where('user_id', $roster->user_id ?: $roster->cid)
                ->lockForUpdate()
                ->first();

            if (! $student || ! $student->consolidationIsPending()) {
                return false;
            }

            if ((float) $roster->rating_hours < self::CONSOLIDATION_HOURS) {
                return false;
            }

            // Clearing the rating ends consolidation
            $rating = $student->consolidation_rating;
            $student->update(['consolidation_rating' => null]);
            $nextStage = $this->unlockNextTrainingStage($student, $rating);
            CreateNote::newNote(
                $student->id,
                'Consolidation Completed',
                sprintf(
                    'System unlocked %s after %.2f hours were logged at the %s rating!',
                    $nextStage,
                    (float) $roster->rating_hours,
                    $rating,
                ),
            );

            return true;
        });
    }

    /**
     * S1 Training (DEL/GND) ends without a new rating, so completing its whole
     * checklist unlocks the S2 Training (TWR) package.
     */
    public function completeS1TrainingIfDone(Student $student): bool
    {
        return DB::transaction(function () use ($student): bool {
            $student = Student::with('labels.label')->lockForUpdate()->findOrFail($student->id);

            $onS1Training = $student->labels->contains(
                fn (StudentInteractiveLabels $link): bool => $link->label?->name === 'S1 Training',
            );

            if (! $onS1Training || $student->isRecertification()) {
                return false;
            }

            // An empty checklist (missing mapping) never counts as complete
            if (! $student->checklistItems()->exists() || $student->checklistItems()->where('completed', false)->exists()) {
                return false;
            }

            $label = StudentLabel::where('name', 'S2 Training')->first();

            if (! $label) {
                Log::warning('No S2 Training label exists after S1 Training was completed!', ['student_id' => $student->id]);

                return false;
            }

            $itemCount = $this->assignTrainingPackage($student, $label);

            CreateNote::newNote(
                $student->id,
                'Checklist Assigned Automatically',
                sprintf('System unlocked the S2 Training package with %d checklist items after the S1 Training checklist was completed!', $itemCount),
            );

            return true;
        });
    }

    public function waiveConsolidation(Student $student, User $actor): bool
    {
        return DB::transaction(function () use ($student, $actor): bool {
            $student = Student::with('user.rosterProfile')->lockForUpdate()->findOrFail($student->id);

            if (! $student->isConsolidating()) {
                return false;
            }

            $rating = $student->consolidation_rating;
            $student->update(['consolidation_rating' => null]);
            $nextStage = $this->unlockNextTrainingStage($student, $rating);

            CreateNote::newNote(
                $student->id,
                'Consolidation Waived',
                sprintf(
                    '%s waived the remaining %s consolidation requirement and unlocked %s!',
                    $actor->fullName('FLC'),
                    $rating,
                    $nextStage,
                ),
            );

            return true;
        });
    }

    /** @return Collection<int, StudentLabel> */
    public function recertificationPackages(Student $student): Collection
    {
        $student->loadMissing('user');
        $isVisitor = $student->isVisitor();
        $tierType = $isVisitor && $student->user?->division_code !== 'CAN' ? 'T3' : 'T1';
        $mapClass = $isVisitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;
        $packageNames = Student::recertificationPackagesForRating((int) $student->user?->rating_id);

        if ($packageNames === []) {
            return collect();
        }

        if (! $isVisitor) {
            $mappedLabelIds = $mapClass::where('tier_type', $tierType)->pluck('label_id');
            $packages = StudentLabel::whereIn('id', $mappedLabelIds)
                ->whereIn('name', $packageNames)
                ->get()
                ->sortBy(fn (StudentLabel $label): int => array_search($label->name, $packageNames, true))
                ->values();

            return $packages->count() === count($packageNames) ? $packages : collect();
        }

        $orderedLabelIds = $mapClass::where('tier_type', $tierType)
            ->orderBy('id')
            ->pluck('label_id')
            ->unique()
            ->values();

        $packages = StudentLabel::whereIn('id', $orderedLabelIds)
            ->where('visitor', true)
            ->whereNotIn('name', ['Visitor Waitlist', 'Visitor VATCAN', 'Visitor Non-VATCAN'])
            ->get()
            ->sortBy(fn (StudentLabel $label): int => $orderedLabelIds->search($label->id))
            ->take(count($packageNames))
            ->values();

        return $packages->count() === count($packageNames) ? $packages : collect();
    }

    public function startRecertification(Student $student, User $actor): bool
    {
        $student->loadMissing('user');
        $isVisitor = (int) $student->status === Student::STATUS_VISITOR_WAITLIST;
        $tierType = $isVisitor && $student->user?->division_code !== 'CAN' ? 'T3' : 'T1';
        $packages = $this->recertificationPackages($student);
        $requiredPackageCount = count(Student::recertificationPackagesForRating((int) $student->user?->rating_id));

        $eligible = (int) $student->user?->rating_id >= 2
            && $student->isWaitlisted()
            && $packages->count() === $requiredPackageCount;

        if (! $eligible) {
            return false;
        }

        return DB::transaction(function () use ($student, $actor): bool {
            $student = Student::with(['labels.label', 'user'])
                ->lockForUpdate()
                ->findOrFail($student->id);
            $isVisitor = (int) $student->status === Student::STATUS_VISITOR_WAITLIST;
            $tierType = $isVisitor && $student->user?->division_code !== 'CAN' ? 'T3' : 'T1';
            $packages = $this->recertificationPackages($student);
            $requiredPackageCount = count(Student::recertificationPackagesForRating((int) $student->user?->rating_id));

            if (
                ! $student->isWaitlisted()
                || (int) $student->user?->rating_id < 2
                || $student->isRecertification()
                || $packages->count() !== $requiredPackageCount
            ) {
                return false;
            }

            $recertificationLabel = StudentLabel::where('name', Student::RECERTIFICATION_LABEL)->first();

            if (! $recertificationLabel) {
                Log::error('The recertification workflow label is missing!', ['student_id' => $student->id]);

                return false;
            }

            $itemCount = $this->assignRecertificationPackages($student, $packages, $isVisitor, $tierType);
            $packageNames = $packages->pluck('name')->join(', ');

            $student->addLabel($recertificationLabel);

            $student->update([
                'status' => $isVisitor ? Student::STATUS_VISITOR_ACTIVE : Student::STATUS_ACTIVE,
                'consolidation_rating' => null,
            ]);

            CreateNote::newNote(
                $student->id,
                'Recertification Started',
                sprintf(
                    '<u>%s</u> started recertification through %s with %d checklist items! The controller rating was not changed!',
                    e($actor->fullName('FLC')),
                    e($packageNames),
                    $itemCount,
                ),
                $actor->activeInstructorProfile?->id ?? 1,
            );

            return true;
        });
    }

    public function completeRecertification(Student $student, User $actor): string|false|null
    {
        return DB::transaction(function () use ($student, $actor): string|false|null {
            $student = Student::with(['labels.label', 'user'])
                ->lockForUpdate()
                ->findOrFail($student->id);

            if (! $student->isRecertification()) {
                return null;
            }

            $isVisitor = $student->isVisitor();
            $tierType = $isVisitor && $student->user?->division_code !== 'CAN' ? 'T3' : 'T1';
            $completedPackages = $this->recertificationPackages($student);
            $nextPackage = $isVisitor
                ? $this->nextVisitorPackage($completedPackages->last(), $tierType)
                : StudentLabel::where('name', self::NEXT_TRAINING_PACKAGE[$student->user?->rating_short] ?? '')->first();

            if ((int) $student->user?->rating_id < 5 && ! $nextPackage) {
                Log::error('The normal training package after recertification is missing!', [
                    'student_id' => $student->id,
                    'rating' => $student->user?->rating_short,
                    'visitor' => $isVisitor,
                ]);

                return false;
            }

            $student->labels()
                ->whereHas('label', fn ($query) => $query->where('name', Student::RECERTIFICATION_LABEL))
                ->delete();

            if ($nextPackage) {
                $itemCount = $this->assignTrainingPackage($student, $nextPackage, $isVisitor, $tierType);

                CreateNote::newNote(
                    $student->id,
                    'Recertification Completed',
                    sprintf(
                        '<u>%s</u> completed recertification through %s and moved the controller into normal %s with %d checklist items!',
                        e($actor->fullName('FLC')),
                        e($student->user?->rating_short ?? 'their current rating'),
                        e($nextPackage->name),
                        $itemCount,
                    ),
                    $actor->activeInstructorProfile?->id ?? 1,
                );

                return $nextPackage->name;
            }

            $student->markTrainingCompleted();

            CreateNote::newNote(
                $student->id,
                'Recertification Completed',
                sprintf(
                    '<u>%s</u> completed recertification through %s! The controller is now fully certified!',
                    e($actor->fullName('FLC')),
                    e($student->user?->rating_short ?? 'their current rating'),
                ),
                $actor->activeInstructorProfile?->id ?? 1,
            );

            return null;
        });
    }

    private function nextVisitorPackage(?StudentLabel $completedPackage, string $tierType): ?StudentLabel
    {
        if (! $completedPackage) {
            return null;
        }

        $orderedLabelIds = LabelChecklistVisitorMap::where('tier_type', $tierType)
            ->orderBy('id')
            ->pluck('label_id')
            ->unique()
            ->values();
        $currentIndex = $orderedLabelIds->search($completedPackage->id);

        if ($currentIndex === false) {
            return null;
        }

        $nextLabelId = $orderedLabelIds->get($currentIndex + 1);

        return $nextLabelId ? StudentLabel::find($nextLabelId) : null;
    }

    /** @param Collection<int, StudentLabel> $packages */
    private function assignRecertificationPackages(
        Student $student,
        Collection $packages,
        bool $visitor,
        string $tierType,
    ): int {
        $exclusiveLabelIds = StudentLabel::where('exclusive', true)->pluck('id');
        $student->labels()->whereIn('student_label_id', $exclusiveLabelIds)->delete();

        $highestPackage = $packages->last();
        $student->addLabel($highestPackage);

        $student->checklistItems()->delete();
        $mapClass = $visitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;
        $checklistItemIds = $mapClass::whereIn('label_id', $packages->pluck('id'))
            ->where('tier_type', $tierType)
            ->with('checklist.items')
            ->get()
            ->flatMap(fn (LabelChecklistMap|LabelChecklistVisitorMap $map) => $map->checklist?->items->pluck('id') ?? collect())
            ->unique();

        foreach ($checklistItemIds as $checklistItemId) {
            StudentChecklistItem::create([
                'student_id' => $student->id,
                'checklist_item_id' => $checklistItemId,
                'completed' => false,
            ]);
        }

        return $checklistItemIds->count();
    }

    private function beginConsolidation(Student $student, string $rating): void
    {
        $requiredHours = self::CONSOLIDATION_HOURS;

        $student->update(['consolidation_rating' => $rating]);

        CreateNote::newNote(
            $student->id,
            'Rating Promotion Detected',
            sprintf(
                'System detected promotion to %s, reset rating hours, and started the %.0f-hour consolidation period!',
                $rating,
                $requiredHours,
            ),
        );
    }

    private function unlockNextTrainingStage(Student $student, string $rating, bool $afterConsolidation = true): string
    {
        $labelName = self::NEXT_TRAINING_PACKAGE[$rating] ?? null;

        if ($labelName === null) {
            $student->update(['status' => Student::STATUS_COMPLETED]);
            CreateNote::newNote(
                $student->id,
                'Training Completed Automatically',
                sprintf('VATCAN posted the %s rating! The controller is now fully certified!', $rating),
            );

            return 'completed training status';
        }

        $label = StudentLabel::where('name', $labelName)->first();

        if (! $label) {
            Log::warning('No next-stage training label exists after consolidation!', [
                'student_id' => $student->id,
                'rating' => $rating,
                'expected_label' => $labelName,
            ]);

            return 'the next training stage';
        }

        $itemCount = $this->assignTrainingPackage($student, $label);

        CreateNote::newNote(
            $student->id,
            'Checklist Assigned Automatically',
            $afterConsolidation
                ? sprintf('System unlocked the %s package with %d checklist items after consolidation!', $labelName, $itemCount)
                : sprintf('System unlocked the %s package with %d checklist items immediately after the S1 promotion!', $labelName, $itemCount),
        );

        return $labelName;
    }

    /** Replaces the student's exclusive package label and checklist with $label's; returns the item count */
    public function assignTrainingPackage(
        Student $student,
        StudentLabel $label,
        bool $visitor = false,
        string $tierType = 'T1',
    ): int {
        $exclusiveLabelIds = StudentLabel::where('exclusive', true)->pluck('id');
        $student->labels()->whereIn('student_label_id', $exclusiveLabelIds)->delete();
        $student->addLabel($label);

        $student->checklistItems()->delete();

        $mapClass = $visitor ? LabelChecklistVisitorMap::class : LabelChecklistMap::class;
        $checklistItemIds = $mapClass::where('label_id', $label->id)
            ->where('tier_type', $tierType)
            ->with('checklist.items')
            ->get()
            ->flatMap(fn (LabelChecklistMap|LabelChecklistVisitorMap $map) => $map->checklist?->items->pluck('id') ?? collect())
            ->unique();

        foreach ($checklistItemIds as $checklistItemId) {
            StudentChecklistItem::firstOrCreate([
                'student_id' => $student->id,
                'checklist_item_id' => $checklistItemId,
            ], ['completed' => false]);
        }

        if ($label->new_status !== null) {
            $student->update(['status' => $label->new_status]);
        }

        return $checklistItemIds->count();
    }
}
