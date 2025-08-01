<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\RenewalNotification;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use App\Models\AtcTraining\StudentLabel;
use App\Models\AtcTraining\StudentInteractiveLabels;
use Carbon\Carbon;
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
            ->where(function($query) use ($days) {
                $query->where('renewed_at', '<=', Carbon::now()->subDays($days));
            })
            ->get();

        foreach ($students as $student) {
            $token = Str::random(31);
            $student->renewal_token = $token;
            $student->renewed_at = Carbon::now();
            $student->save();

            $student->user->notify(new RenewalNotification($student));
        }

        $expirationDays = 11;
        $expiredStudents = Student::whereIn('status', [0, 3])
            ->whereNotNull('renewed_at')
            ->where('renewed_at', '<=', Carbon::now()->subDays($expirationDays))
            ->get();

        foreach ($expiredStudents as $student) {
            $label = StudentLabel::where('name', 'Marked for Removal')->first();
            if ($label) {
                StudentInteractiveLabels::create([
                    'student_label_id' => $label->id,
                    'student_id' => $student->id,
                ]);
            }
            StudentNote::create([
                'student_id' => $student->id,
                'author_id' => 1,
                'title' => 'Renewal Failed',
                'content' => 'Student did not respond in time and has been marked for removal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $student->status = 4;
            $student->save();
        }
    }
}
