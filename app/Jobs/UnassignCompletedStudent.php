<?php

namespace App\Jobs;

use App\Models\AtcTraining\Student;
use App\Services\VatcanClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UnassignCompletedStudent implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 3600];

    public function __construct(private readonly int $studentId) {}

    public function handle(VatcanClient $vatcan): void
    {
        $student = Student::find($this->studentId);

        if ($student && (int) $student->status === Student::STATUS_COMPLETED && $student->instructor_id === null) {
            $vatcan->unassign((int) $student->user_id);
        }
    }
}
