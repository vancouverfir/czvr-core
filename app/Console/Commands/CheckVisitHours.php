<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Settings\CoreSettings;
use App\Notifications\network\CheckVisitHours as Email;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class CheckVisitHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:visit-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if controllers have put 50% of their time on Vancouver positions each quarter';

    /**
     * Use 7 seconds to stay under the VATSIM API limit.
     */
    private const REQUEST_DELAY_SECONDS = 7;

    /**
     * Number of attempts per controller before putting them in the unknown list.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Maximum time to sleep between retries.
     */
    private const MAX_BACKOFF_SECONDS = 300;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $members = [];
        $unknown = [];

        $fields = [
            'delgnd',
            'delgnd_t2',
            'twr',
            'twr_t2',
            'dep',
            'app',
            'app_t2',
            'ctr',
            'fss',
        ];

        $quarterAgo = Carbon::now()->subMonths(3)->format('Y-m-d');

        $rosterMembers = RosterMember::where('visit', 0)
            ->where(function ($query) use ($fields): void {
                foreach ($fields as $field) {
                    $query->orWhere($field, '!=', 0);
                }
            })
            ->get();

        foreach ($rosterMembers as $r) {
            $minutes = $this->getVatsimMinutes($r->cid, $quarterAgo);

            if ($minutes === null) {
                $unknown[] = [
                    'name' => $r->full_name.' '.$r->cid,
                    'cid' => $r->cid,
                ];

                continue;
            }

            $hours = $minutes / 60;

            $quotient = $hours <= 0
                ? 0
                : round($r->currency / $hours, 3);

            // Vancouver Hours / VATSIM Total is less than 50%
            if ($quotient > 0 && $quotient < 0.5) {
                $members[] = [
                    'percentage' => $quotient,
                    'name' => $r->full_name.' '.$r->cid,
                ];
            }
        }

        usort($members, fn ($a, $b) => $a['percentage'] <=> $b['percentage']);

        $settings = CoreSettings::find(1);

        if (! $settings) {
            Log::error('Visit-hours report could not be sent because CoreSettings record 1 does not exist.');

            return self::SUCCESS;
        }

        try {
            Notification::route('mail', array_filter([
                $settings->emailfirchief,
                $settings->emaildepfirchief,
                $settings->emailcinstructor,
            ]))->notify(new Email($members, $unknown));
        } catch (Throwable $e) {
            Log::error('Visit-hours report email failed to send.', [
                'message' => $e->getMessage(),
            ]);

            return self::SUCCESS;
        }

        if (count($unknown) > 0) {
            Log::warning('Visit-hours report sent with unknown VATSIM results.', [
                'unknown_count' => count($unknown),
                'unknown' => $unknown,
            ]);
        }

        return self::SUCCESS;
    }

    private function getVatsimMinutes(int|string $cid, string $date): ?int
    {
        $url = sprintf(
            'https://api.vatsim.net/api/ratings/%s/atcsessions/',
            $cid
        );

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $this->waitForVatsimRateLimitSlot();

            try {
                $response = Http::acceptJson()
                    ->withUserAgent('vancouver-visit-hours-checker')
                    ->connectTimeout(20)
                    ->timeout(60)
                    ->get($url, [
                        'start' => $date,
                    ]);
            } catch (Throwable $e) {
                Log::warning("VATSIM API exception for controller {$cid}.", [
                    'attempt' => $attempt,
                    'message' => $e->getMessage(),
                ]);

                $this->sleepBeforeRetry($attempt);

                continue;
            }

            if ($response->successful()) {
                $contents = $response->object();

                return collect($contents->results ?? [])
                    ->sum(fn ($result) => (int) ($result->minutes_on_callsign ?? 0));
            }

            if ($response->status() === 429) {
                $retryAfter = $this->getRetryAfterSeconds($response->header('Retry-After'), $attempt);

                Log::warning("VATSIM API rate limited controller {$cid}.", [
                    'attempt' => $attempt,
                    'retry_after' => $retryAfter,
                ]);

                sleep($retryAfter);

                continue;
            }

            if ($response->serverError()) {
                Log::warning("VATSIM API server error for controller {$cid}.", [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $this->sleepBeforeRetry($attempt);

                continue;
            }

            Log::warning("VATSIM API failed for controller {$cid}.", [
                'attempt' => $attempt,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $this->sleepBeforeRetry($attempt);
        }

        return null;
    }

    private function waitForVatsimRateLimitSlot(): void
    {
        sleep(self::REQUEST_DELAY_SECONDS);
    }

    private function sleepBeforeRetry(int $attempt): void
    {
        sleep(min(self::MAX_BACKOFF_SECONDS, 30 * $attempt));
    }

    private function getRetryAfterSeconds(?string $retryAfterHeader, int $attempt): int
    {
        if (is_numeric($retryAfterHeader)) {
            return max(1, (int) $retryAfterHeader);
        }

        if ($retryAfterHeader) {
            $timestamp = strtotime($retryAfterHeader);

            if ($timestamp !== false) {
                return max(1, $timestamp - time());
            }
        }

        return min(self::MAX_BACKOFF_SECONDS, 60 * $attempt);
    }
}
