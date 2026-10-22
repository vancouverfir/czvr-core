<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class VatcanInstructorClient
{
    public function assign(int $studentCid, int $instructorCid, int $assignedBy): void
    {
        $this->post("user/{$studentCid}/instructor/assign", [
            'instructor_cid' => $instructorCid,
            'assigned_by' => $assignedBy,
        ]);
    }

    public function unassign(int $studentCid): void
    {
        $this->post("user/{$studentCid}/instructor/unassign");
    }

    private function post(string $path, array $payload = []): void
    {
        $apiKey = trim((string) config('services.vatcan.key'));
        $apiUrl = 'https://vatcan.ca/api/v2';

        if ($apiKey === '') {
            throw new RuntimeException('The VATCAN API key is not configured!');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeaders(['Authorization' => 'Token '.$apiKey])
            ->connectTimeout(5)
            ->timeout(20)
            ->retry(2, 500, throw: false)
            ->post($apiUrl.'/'.ltrim($path, '/'), $payload);

        if ($response->failed()) {
            throw new RuntimeException('VATCAN rejected the instructor update (HTTP '.$response->status().')!', $response->status());
        }
    }
}
