<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\Student;
use App\Services\VatcanClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class FetchVatcanNotes extends Command
{
    protected $signature = 'vancouver:fetch-vatcan-notes';

    protected $description = 'Fetch and cache training notes from Vatcan v2 API';

    public function handle(VatcanClient $vatcan): int
    {
        if (trim((string) config('services.vatcan.key')) === '') {
            $this->error('VATCAN API key is not configured!');

            return self::FAILURE;
        }

        $students = Student::query()->orderBy('id')->get();
        $updated = 0;
        $unchanged = 0;
        $failed = 0;

        foreach ($students as $index => $student) {
            if ($index > 0) {
                // VATCAN throttling sustained bursts at roughly 15 calls per minute
                Sleep::sleep(4);
            }

            $userId = (int) $student->user_id;

            try {
                if ($vatcan->refreshNotes($userId, waitOnRateLimit: true) === 'unchanged') {
                    $unchanged++;
                    $this->line("Unchanged notes for user {$userId}");
                } else {
                    $updated++;
                    $this->info("Updated notes for user {$userId}");
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->warn("Skipped user {$userId}: {$e->getMessage()}");
                Log::warning('VATCAN notes request failed!', [
                    'user_id' => $userId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("VATCAN notes sync complete: {$updated} updated, {$unchanged} unchanged, {$failed} failed!");

        return $updated > 0 || $unchanged > 0 ? self::SUCCESS : self::FAILURE;
    }
}
