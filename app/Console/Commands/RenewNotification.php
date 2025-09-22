<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentInteractiveLabels;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentNote;
use App\Notifications\RenewalExpiredNotification;
use App\Notifications\RenewalNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
    protected $description = 'Send Renewal notifications to all students and visitors on the waitlsit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = 31;
        $students = Student::whereIn('status', [0, 3])
            ->where(function ($query) use ($days) {
                $query->where('renewed_at', '<=', Carbon::now()->subDays($days));
            })
            ->whereNull('renewal_notified_at')
            ->get();

        foreach ($students as $student) {
            $token = Str::random(31);
            $student->renewal_token = $token;
            $student->renewal_notified_at = Carbon::now();
            $student->save();
            $student->user->notify(new RenewalNotification($student));
        }

        $expirationDays = 14;
        $expiredStudents = Student::whereIn('status', [0, 3])
            ->whereNotNull('renewal_notified_at')
            ->where('renewal_notified_at', '<=', Carbon::now()->subDays($expirationDays))
            ->get();

        foreach ($expiredStudents as $student) {
            $labels = StudentLabel::where('new_status', 4)->get();

            foreach ($labels as $label) {
                StudentInteractiveLabels::create([
                    'student_label_id' => $label->id,
                    'student_id' => $student->id,
                ]);
            }
            StudentInteractiveLabels::create([
                'student_label_id' => 10,
                'student_id' => $student->id,
            ]);
            StudentNote::create([
                'student_id' => $student->id,
                'author_id' => 1,
                'title' => 'Renewal Timed Out',
                'content' => 'Student did not respond in time and has been marked for removal!',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $student->status = 4;
            $student->renewal_notified_at = null;
            $student->save();
            $student->user->notify(new RenewalExpiredNotification($student));
        }
    }
}
