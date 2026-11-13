<?php

namespace App\Console\Commands;

use App\Classes\HttpHelper;
use App\Classes\VatsimHelper;
use App\Models\Users\User;
use App\Services\TrainingProgressionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RatingUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:rating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs VATSIM rating update';

    /**
     * Execute the console command.
     */
    public function handle(TrainingProgressionService $progression): int
    {
        $ratings = [];

        $response = HttpHelper::getClient()
            ->retry(2, 250, throw: false)
            ->get(VatsimHelper::getDatafeedUrl());

        if (! $response->successful()) {
            Log::warning('VATSIM ratings datafeed request failed!', ['status' => $response->status()]);

            return self::FAILURE;
        }

        $vatsimRatings = $response->json('ratings');

        if (! is_array($vatsimRatings)) {
            Log::warning('VATSIM ratings datafeed did not contain a ratings array!');

            return self::FAILURE;
        }

        foreach ($vatsimRatings as $r) {
            if (! is_array($r) || ! isset($r['id'], $r['short'], $r['long'])) {
                continue;
            }

            $ratings[$r['id']] = [
                'short' => $r['short'],
                'long' => $r['long'],
            ];
        }

        $users = User::where('id', '!=', 1)->where('id', '!=', 2)->get();

        // Check each user to see if their rating has changed
        foreach ($users as $u) {
            $ratingResponse = HttpHelper::getClient()
                ->retry(2, 250, throw: false)
                ->get('https://api.vatsim.net/api/ratings/'.$u->id.'/');

            if (! $ratingResponse->successful()) {
                Log::warning('VATSIM user rating request failed!', [
                    'cid' => $u->id,
                    'status' => $ratingResponse->status(),
                ]);

                continue;
            }

            $ratingID = $ratingResponse->json('rating');

            if ((! is_int($ratingID) && ! (is_string($ratingID) && ctype_digit($ratingID))) || ! isset($ratings[(int) $ratingID])) {
                Log::warning('VATSIM user rating response was invalid!', [
                    'cid' => $u->id,
                ]);

                continue;
            }

            $ratingID = (int) $ratingID;

            if ($u->rating_id != $ratingID) {
                // Log it for when it breaks
                Log::info('User: '.$u->fname.' '.$u->lname.' updated from '.$u->rating_short.' to '.$ratings[$ratingID]['short'].'!');

                $progression->synchronizeRating($u, $ratingID, $ratings[$ratingID]);

                Log::info('--> Completed!');
            } else {
                // Log it for when it breaks
                Log::info('User: '.$u->fname.' '.$u->lname.' rating is unchanged! Skipping!');
            }
        }

        return self::SUCCESS;
    }
}
