<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\Student;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class FetchVatcanNotes extends Command
{
    protected $signature = 'vancouver:fetch-vatcan-notes';

    protected $description = 'Fetch and cache training notes from Vatcan v2 API';

    public function handle(): int
    {
        $apiKey = config('services.vatcan.key');
        $apiUrl = 'https://vatcan.ca/api/v2';

        if (! is_string($apiKey) || $apiKey === '') {
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

            $userId = $student->user_id;
            $cacheKey = "vatcan_notes_{$userId}";
            $etagKey = "vatcan_notes_etag_{$userId}";
            $headers = ['Authorization' => 'Token '.$apiKey];

            if ($etag = Cache::get($etagKey)) {
                $headers['If-None-Match'] = $etag;
            }

            try {
                $response = $this->requestNotes("{$apiUrl}/user/{$userId}/notes", $headers, $userId);

                if ($response->status() === 304 && Cache::has($cacheKey)) {
                    $unchanged++;
                    $this->line("Unchanged notes for user {$userId}");

                    continue;
                }

                if ($response->status() === 304) {
                    unset($headers['If-None-Match']);
                    $response = $this->requestNotes("{$apiUrl}/user/{$userId}/notes", $headers, $userId);
                }

                if (! $response->successful()) {
                    $failed++;
                    $this->warn("Skipped user {$userId}: VATCAN returned HTTP {$response->status()}!");
                    Log::warning('VATCAN notes request failed!', [
                        'user_id' => $userId,
                        'status' => $response->status(),
                    ]);

                    continue;
                }

                $data = $response->json();
                if (! is_array($data) || ! array_key_exists('notes', $data) || ! is_array($data['notes'])) {
                    $failed++;
                    $this->warn("Skipped user {$userId}: VATCAN returned an unexpected notes payload!");
                    Log::warning('VATCAN notes response had an unexpected payload!', [
                        'user_id' => $userId,
                    ]);

                    continue;
                }

                $notes = $data['notes'];
                usort($notes, fn ($a, $b) => strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? ''));

                Cache::put($cacheKey, $notes, now()->addDays(7));
                if ($newEtag = $response->header('ETag')) {
                    Cache::put($etagKey, $newEtag, now()->addDays(7));
                }

                $updated++;
                $this->info("Updated notes for user {$userId} (".count($notes).' notes)');
            } catch (\Throwable $e) {
                $failed++;
                $this->error("Failed to fetch notes for user {$userId}!");
                Log::warning('VATCAN notes request raised an exception!', [
                    'user_id' => $userId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("VATCAN notes sync complete: {$updated} updated, {$unchanged} unchanged, {$failed} failed!");

        return $updated > 0 || $unchanged > 0 ? self::SUCCESS : self::FAILURE;
    }

    private function requestNotes(string $url, array $headers, int $userId): Response
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $response = Http::acceptJson()
                ->withHeaders($headers)
                ->connectTimeout(5)
                ->timeout(15)
                ->get($url);

            if ($response->status() !== 429 || $attempt === 3) {
                return $response;
            }

            $retryAfter = max(1, (int) ($response->header('Retry-After') ?: 60));
            $this->warn("VATCAN rate-limited user {$userId}; retrying in {$retryAfter} seconds!");
            Sleep::sleep($retryAfter);
        }
    }
}
