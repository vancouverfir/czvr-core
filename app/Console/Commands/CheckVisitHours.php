<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Settings\CoreSettings;
use App\Notifications\network\CheckVisitHours as Email;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use GuzzleHttp\Exception\RequestException;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $members = [];

        function getUrl($cid, $date)
        {
            return sprintf('https://api.vatsim.net/api/ratings/%s/atcsessions/?start=%s', $cid, $date);
        }

        foreach (RosterMember::where('visit', 0)->get() as $r) {
            $fields = ['delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr', 'fss'];

            $fieldIsNotZero = false;
            foreach ($fields as $field) {
                if ($r->$field != 0) {
                    $fieldIsNotZero = true;
                    break;
                }
            }

            if (!$fieldIsNotZero) {
                continue;
            }

            $quarterAgo = Carbon::now()->subMonths(3)->format('Y-m-d');
            $minutes = 0;

            try {
                $client = new Client();
                $response = $client->request('GET', getUrl($r->cid, $quarterAgo));
                $contents = json_decode($response->getBody()->getContents());

                foreach ($contents->results as $result) {
                    $minutes += $result->minutes_on_callsign;
                }
            } catch (RequestException $e) {
                \Log::error("Error with VATSIM API for controller {$r->cid}: " . $e->getMessage());
                continue;
            }

            // Change to hours as that is how it's stored in the roster
            $hours = $minutes / 60;

            $quotient = $hours == 0 ? 0 : round($r->currency / $hours, 3);

            // Vancouver Hours / VATSIM Total is less than 50%
            if ($quotient < 0.5) {
                $name = $r->full_name.' '.$r->cid;

                $members[] = [
                    'percentage' => $quotient,
                    'name' => $name,
                ];
            }
        }

        krsort($members);

        $settings = CoreSettings::find(1);
        Notification::route('mail', [
            $settings->emailfirchief,
            $settings->emaildepfirchief,
            $settings->emailcinstructor,
        ])->notify(new Email($members));
    }
}
