<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use RuntimeException;
use Throwable;

class VatcanClient
{
    public const SESSION_TYPES = ['Sweatbox', 'OJT (Monitoring)', 'OTS', 'Generic'];

    public const TIER_2_ENDORSEMENT = 'T2';

    public static function url(string $path = ''): string
    {
        return rtrim((string) config('services.vatcan.url'), '/').'/'.ltrim($path, '/');
    }

    public function assign(int $studentCid, int $instructorCid, int $assignedBy): void
    {
        $this->send('post', "user/{$studentCid}/instructor/assign", [
            'instructor_cid' => $instructorCid,
            'assigned_by' => $assignedBy,
        ]);
    }

    public function unassign(int $studentCid): void
    {
        $this->send('post', "user/{$studentCid}/instructor/unassign");
    }

    public function createNote(int $studentCid, int $instructorCid, string $position, string $note, int $sessionType): void
    {
        $this->send('post', "user/{$studentCid}/notes/create", [
            'instructor_cid' => $instructorCid,
            'position' => $position,
            'note' => $note,
            'session_type' => $sessionType,
        ]);
    }

    public function updateNote(int $studentCid, int $noteId, int $instructorCid, string $position, string $note, int $sessionType): void
    {
        $this->send('patch', "user/{$studentCid}/notes/{$noteId}", [
            'instructor_cid' => $instructorCid,
            'position' => $position,
            'note' => $note,
            'session_type' => $sessionType,
        ]);
    }

    public function addEndorsement(int $cid, string $endorsement, ?string $position, int $assignedBy): void
    {
        $this->send('post', 'facility/endorsement/add', array_filter([
            'cid' => $cid,
            'endorsement' => $endorsement,
            'position' => $position,
            'assigned_by' => $assignedBy,
        ], fn ($value) => $value !== null));
    }

    public function removeEndorsement(int $cid, string $endorsement): void
    {
        try {
            $this->send('post', 'facility/endorsement/remove', ['cid' => $cid, 'endorsement' => $endorsement]);
        } catch (RuntimeException $exception) {
            if ($exception->getCode() !== 404) {
                throw $exception;
            }
        }
    }

    /** @return 'updated'|'unchanged' */
    public function refreshNotes(int $cid, bool $waitOnRateLimit = false): string
    {
        $cacheKey = "vatcan_notes_{$cid}";
        $etagKey = "vatcan_notes_etag_{$cid}";
        $etag = Cache::has($cacheKey) ? Cache::get($etagKey) : null;

        $response = $this->getNotes($cid, $etag, $waitOnRateLimit);

        if ($response->status() === 304) {
            return 'unchanged';
        }

        if (! $response->successful()) {
            throw new RuntimeException("VATCAN returned HTTP {$response->status()} for the notes!", $response->status());
        }

        $notes = $response->json('notes');

        if (! is_array($notes)) {
            throw new RuntimeException('VATCAN returned an unexpected notes payload!');
        }

        usort($notes, fn ($a, $b) => strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? ''));
        Cache::put($cacheKey, $notes, now()->addDays(7));

        if ($newEtag = $response->header('ETag')) {
            Cache::put($etagKey, $newEtag, now()->addDays(7));
        }

        return 'updated';
    }

    private function getNotes(int $cid, ?string $etag, bool $waitOnRateLimit): Response
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $response = $this->request()
                ->withHeaders($etag ? ['If-None-Match' => $etag] : [])
                ->timeout(15)
                ->get(self::url("user/{$cid}/notes"));

            if ($response->status() !== 429 || ! $waitOnRateLimit || $attempt === 3) {
                return $response;
            }

            $retryAfter = max(1, (int) ($response->header('Retry-After') ?: 60));
            Log::info('VATCAN rate-limited the notes request, retrying!', ['cid' => $cid, 'seconds' => $retryAfter]);
            Sleep::sleep($retryAfter);
        }
    }

    private function request(): PendingRequest
    {
        $apiKey = trim((string) config('services.vatcan.key'));

        if ($apiKey === '') {
            throw new RuntimeException('The VATCAN API key is not configured!');
        }

        return Http::acceptJson()
            ->withHeaders(['Authorization' => 'Token '.$apiKey])
            ->connectTimeout(5)
            ->timeout(20);
    }

    private function send(string $method, string $path, array $payload = []): void
    {
        $response = $this->request()
            ->asJson()
            ->retry(2, 500, fn (Throwable $exception) => $exception instanceof ConnectionException, throw: false)
            ->{$method}(self::url($path), $payload);

        if ($response->failed()) {
            $reason = trim((string) ($response->json('error') ?? ''));

            throw new RuntimeException(
                'VATCAN rejected the request (HTTP '.$response->status().')'.($reason !== '' ? ': '.str($reason)->limit(200) : '!'),
                $response->status(),
            );
        }
    }
}
