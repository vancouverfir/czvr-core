<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\Student;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchVatcanNotes extends Command
{
    protected $signature = 'vancouver:fetch-vatcan-notes';
    protected $description = 'Fetch and cache training notes from Vatcan v2 API';

    public function handle()
    {
        $apiKey = env('VATCAN_API_KEY');
        $client = new Client();
        $students = Student::all();
        $chunks = $students->chunk(50);
        $total = $chunks->count();
        foreach ($chunks as $index => $chunk) {
            foreach ($chunk as $student) {
                $userId = $student->user_id;
                $cacheKey = "vatcan_notes_{$userId}";
                $etagKey = "vatcan_notes_etag_{$userId}";
                $headers = ['Authorization' => 'Token '.$apiKey];
                if ($etag = Cache::get($etagKey)) {
                    $headers['If-None-Match'] = $etag;
                }
                try {
                    $response = $client->get("https://vatcan.ca/api/v2/user/{$userId}/notes", [
                        'headers' => $headers,
                        'http_errors' => false,
                    ]);
                    if ($response->getStatusCode() === 304) {
                        $this->info("No changes for user {$userId}");
                        continue;
                    }
                    if ($newEtag = $response->getHeaderLine('ETag')) {
                        Cache::put($etagKey, $newEtag, now()->addDays(7));
                    }
                    $data = json_decode($response->getBody()->getContents(), true);
                    $notes = $data['notes'] ?? [];
                    usort($notes, fn ($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
                    Cache::put($cacheKey, $notes, now()->addDays(7));
                    $this->info("Updated notes for user {$userId}");
                } catch (\Exception $e) {
                    $this->error("Failed for user {$userId}: ".$e->getMessage());
                    \Log::error('Vatcan notes fetch failed: '.$e->getMessage());
                }
            }
            if ($index < $total - 1) {
                $this->info('Rate limit pause...');
                sleep(60);
            }
        }
    }
}
