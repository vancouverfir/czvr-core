<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentNote;
use App\Notifications\RenewalExpiredNotification;
use App\Notifications\RenewalNotification;
use App\Notifications\RenewalReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RenewNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:renewalnotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal notifications to students and visitors on the waitlist';

    /** Days on the waitlist before a renewal notice is sent */
    public const RENEWAL_INTERVAL_DAYS = 31;

    /** Days a student has to answer the renewal notice before being marked for removal */
    public const RENEWAL_GRACE_DAYS = 14;

    /** How many days before the renewal expires is the reminder sent */
    public const RENEWAL_REMINDER_DAYS = 3;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $intervalDays = self::RENEWAL_INTERVAL_DAYS;
        $graceDays = self::RENEWAL_GRACE_DAYS;
        $reminderDays = self::RENEWAL_REMINDER_DAYS;
        $now = Carbon::now();

        $studentsToUpdate = Student::whereIn('status', Student::WAITLIST_STATUSES)
            ->whereNull('renewed_at')
            ->get();

        foreach ($studentsToUpdate as $student) {
            try {
                DB::transaction(function () use ($student, $now): void {
                    $student = Student::lockForUpdate()->findOrFail($student->id);
                    $expiredLabelIds = $this->expiredLabelIds();

                    StudentInteractiveLabels::where('student_id', $student->id)
                        ->whereIn('student_label_id', StudentLabel::where('exclusive', true)->pluck('id'))
                        ->delete();

                    if ($expiredLabelIds->isNotEmpty()) {
                        StudentInteractiveLabels::where('student_id', $student->id)
                            ->whereIn('student_label_id', $expiredLabelIds)
                            ->delete();
                    }

                    $student->addLabel($student->status === Student::STATUS_VISITOR_WAITLIST ? 'Visitor Waitlist' : 'Waitlist');

                    $student->update([
                        'renewed_at' => $now,
                        'renewal_token' => null,
                        'renewal_notified_at' => null,
                        'renewal_reminded_at' => null,
                    ]);

                    StudentNote::create([
                        'student_id' => $student->id,
                        'title' => 'Reinstated Automatically',
                        'content' => 'Student was reinstated automatically after they were unmarked for removal!',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
            } catch (Throwable $exception) {
                $this->logStudentFailure('reinstatement', $student, $exception);
            }
        }

        $students = Student::whereIn('status', Student::WAITLIST_STATUSES)
            ->where('renewed_at', '<=', $now->copy()->subDays($intervalDays))
            ->whereNull('renewal_notified_at')
            ->with('user')
            ->get();

        foreach ($students as $student) {
            try {
                DB::transaction(function () use ($student, $intervalDays, $now): void {
                    $student = Student::with('user')->lockForUpdate()->findOrFail($student->id);

                    if (! $student->isWaitlisted()
                        || $student->renewal_notified_at !== null
                        || ! $student->renewed_at
                        || $student->renewed_at->gt($now->copy()->subDays($intervalDays))) {
                        return;
                    }

                    if (! $student->user || blank($student->user->email)) {
                        throw new \RuntimeException('Student has no deliverable email address!');
                    }

                    $student->update([
                        'renewal_token' => $this->uniqueToken(),
                        'renewal_notified_at' => $now,
                        'renewal_reminded_at' => null,
                    ]);
                    $student->user->notify(new RenewalNotification($student));
                });
            } catch (Throwable $exception) {
                Log::error('Training renewal notification could not be queued!', [
                    'student_id' => $student->id,
                    'user_id' => $student->user_id,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $studentsToRemind = Student::whereIn('status', Student::WAITLIST_STATUSES)
            ->whereNotNull('renewal_token')
            ->whereNotNull('renewal_notified_at')
            ->whereNull('renewal_reminded_at')
            ->where('renewal_notified_at', '<=', $now->copy()->subDays($graceDays - $reminderDays))
            ->where('renewal_notified_at', '>', $now->copy()->subDays($graceDays))
            ->with('user')
            ->get();

        foreach ($studentsToRemind as $student) {
            try {
                DB::transaction(function () use ($student, $now): void {
                    $student = Student::with('user')->lockForUpdate()->findOrFail($student->id);

                    if (! $student->renewalReminderIsDue()) {
                        return;
                    }

                    if (! $student->user || blank($student->user->email)) {
                        throw new \RuntimeException('Student has no deliverable email address!');
                    }

                    $student->update(['renewal_reminded_at' => $now]);
                    $student->user->notify(new RenewalReminderNotification($student));
                });
            } catch (Throwable $exception) {
                $this->logStudentFailure('reminder email', $student, $exception);
            }
        }

        $expiredStudents = Student::whereIn('status', Student::WAITLIST_STATUSES)
            ->whereNotNull('renewal_notified_at')
            ->where('renewal_notified_at', '<=', $now->copy()->subDays($graceDays))
            ->with('user')
            ->get();

        foreach ($expiredStudents as $student) {
            try {
                $expiredStudent = DB::transaction(function () use ($student, $graceDays, $now): ?Student {
                    $student = Student::with('user')->lockForUpdate()->findOrFail($student->id);

                    if (! $student->isWaitlisted()
                        || ! $student->renewal_notified_at
                        || $student->renewal_notified_at->gt($now->copy()->subDays($graceDays))) {
                        return null;
                    }

                    foreach (StudentLabel::whereIn('id', $this->expiredLabelIds())->get() as $label) {
                        $student->addLabel($label);
                    }

                    StudentInteractiveLabels::where('student_id', $student->id)
                        ->whereIn('student_label_id', StudentLabel::whereIn('name', ['Waitlist', 'Visitor Waitlist'])->pluck('id'))
                        ->delete();

                    StudentNote::create([
                        'student_id' => $student->id,
                        'title' => 'Renewal Timed Out',
                        'content' => 'Student did not respond in time and has been marked for removal!',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $student->update([
                        'status' => Student::STATUS_MARKED_FOR_REMOVAL,
                        'renewal_token' => null,
                        'renewal_notified_at' => null,
                        'renewal_reminded_at' => null,
                        'renewed_at' => null,
                        'position' => null,
                    ]);

                    return $student;
                });

                if ($expiredStudent?->user && filled($expiredStudent->user->email)) {
                    try {
                        $expiredStudent->user->notify(new RenewalExpiredNotification($expiredStudent));
                    } catch (Throwable $exception) {
                        $this->logStudentFailure('expiration email', $expiredStudent, $exception);
                    }
                }
            } catch (Throwable $exception) {
                $this->logStudentFailure('expiration', $student, $exception);
            }
        }

        foreach (Student::WAITLIST_STATUSES as $queueStatus) {
            $pos = 1;
            $queueStudents = Student::where('status', $queueStatus)
                ->orderBy('position')
                ->get();

            foreach ($queueStudents as $s) {
                $s->position = $pos++;
                $s->save();
            }
        }

        return Command::SUCCESS;
    }

    private function uniqueToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Student::where('renewal_token', $token)->exists());

        return $token;
    }

    private function expiredLabelIds()
    {
        return StudentLabel::where('new_status', 4)
            ->orWhereIn('name', ['No Response', 'Renewal Expired'])
            ->pluck('id')
            ->unique();
    }

    private function logStudentFailure(string $action, Student $student, Throwable $exception): void
    {
        Log::error("Training renewal {$action} failed!", [
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }
}
