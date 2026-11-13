<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use App\Services\TrainingProgressionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TrainingStatsController extends Controller
{
    private const COMPLETION_NOTE_TITLES = ['Completed Training', 'Training Completed Automatically', 'Recertification Completed'];

    public function index(): View
    {
        $now = now();
        $students = Student::with(['user', 'labels.label', 'instructor.user', 'latestWaitlistContactEvent', 'user.rosterProfile'])
            ->whereIn('status', [
                Student::STATUS_WAITLIST,
                Student::STATUS_ACTIVE,
                Student::STATUS_VISITOR_WAITLIST,
                Student::STATUS_MARKED_FOR_REMOVAL,
                Student::STATUS_VISITOR_ACTIVE,
            ])
            ->get();
        $byStatus = $students->groupBy('status');
        $active = $students->whereIn('status', [Student::STATUS_ACTIVE, Student::STATUS_VISITOR_ACTIVE]);
        $waitlisted = $students->filter(fn (Student $student): bool => $student->isWaitlisted());

        return view('training.stats', [
            'waitlists' => [
                'Home' => $this->waitlistSummary($byStatus->get(Student::STATUS_WAITLIST, collect()), $now),
                'Visitors' => $this->waitlistSummary($byStatus->get(Student::STATUS_VISITOR_WAITLIST, collect()), $now),
            ],
            'homePipeline' => $this->pipeline($byStatus->get(Student::STATUS_ACTIVE, collect()), Student::RATING_TRAINING_LABELS),
            'visitorPipeline' => $this->pipeline($byStatus->get(Student::STATUS_VISITOR_ACTIVE, collect()), Student::trainingOrder(true)),
            'consolidating' => $active->filter(fn (Student $student): bool => $student->consolidationIsPending())
                ->sortByDesc(fn (Student $student): float => $student->consolidationHours())
                ->values(),
            'consolidationHours' => TrainingProgressionService::CONSOLIDATION_HOURS,
            'instructors' => Instructor::with('user')
                ->where('active', true)
                ->withCount([
                    'students as active_students_count' => fn ($query) => $query->whereIn('status', [Student::STATUS_ACTIVE, Student::STATUS_VISITOR_ACTIVE]),
                    'sessions as upcoming_sessions_count' => fn ($query) => $query->where('start_time', '>=', $now),
                ])
                ->get()
                ->sortByDesc('active_students_count')
                ->values(),
            'unassigned' => $active->whereNull('instructor_id')->values(),
            'renewals' => [
                'Awaiting a reply' => $waitlisted->filter(fn (Student $student): bool => $student->hasPendingRenewal())->count(),
                'Reminder sent' => $waitlisted->filter(fn (Student $student): bool => $student->hasPendingRenewal() && $student->renewal_reminded_at !== null)->count(),
                'Deadline within 3 days' => $waitlisted->filter(fn (Student $student): bool => $student->hasPendingRenewal()
                    && $student->renewalDeadlineAt()?->between($now, $now->copy()->addDays(3)))->count(),
                'Marked for removal' => $byStatus->get(Student::STATUS_MARKED_FOR_REMOVAL, collect())->count(),
            ],
            'completions' => $this->completionsPerMonth($now),
        ]);
    }

    /** @return array{count: int, longest: ?int, average: ?int, availability: ?int, contacted: ?int} */
    private function waitlistSummary(Collection $students, Carbon $now): array
    {
        $days = $students->map(fn (Student $student): int => (int) $student->created_at?->diffInDays($now));
        $percent = fn (callable $test): ?int => $students->isEmpty() ? null : (int) round($students->filter($test)->count() / $students->count() * 100);

        return [
            'count' => $students->count(),
            'longest' => $days->max(),
            'average' => $days->isEmpty() ? null : (int) round($days->avg()),
            'availability' => $percent(fn (Student $student): bool => filled($student->availability)),
            'contacted' => $percent(fn (Student $student): bool => $student->latestWaitlistContactEvent?->title === 'Waitlist Contacted'),
        ];
    }

    /** Active students per training package, in package order; recertifications (which also hold a package) are counted on their own */
    private function pipeline(Collection $students, array $packages): array
    {
        $counts = array_fill_keys($packages, 0) + ['Recertification' => 0, 'No package' => 0];

        foreach ($students as $student) {
            $counts[$student->isRecertification() ? 'Recertification' : ($student->currentTrainingLabel($packages) ?? 'No package')]++;
        }

        return $counts;
    }

    /** @return array<string, int> Completions for each of the last 12 months, oldest first */
    private function completionsPerMonth(Carbon $now): array
    {
        $months = collect(range(11, 0))->mapWithKeys(fn (int $back): array => [$now->copy()->startOfMonth()->subMonths($back)->format('M Y') => 0])->all();

        StudentNote::whereIn('title', self::COMPLETION_NOTE_TITLES)
            ->where('created_at', '>=', $now->copy()->startOfMonth()->subMonths(11))
            ->pluck('created_at')
            ->each(function ($createdAt) use (&$months): void {
                $months[Carbon::parse($createdAt)->format('M Y')]++;
            });

        return $months;
    }
}
