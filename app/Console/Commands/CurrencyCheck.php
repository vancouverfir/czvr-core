<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Settings\CoreSettings;
use App\Notifications\network\MonthlyInactivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CurrencyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if every roster member has completed their hours for this quarter';

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
        $badMembersVisit0 = [];
        $badMembersVisit1 = [];

        $fields = ['delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr', 'fss'];

        foreach (RosterMember::all()->sortBy('currency') as $rosterMember) {
            if ($rosterMember->currency >= config(sprintf('currency.%s', $rosterMember->status))) {
                continue;
            }

            $fieldIsNotZero = false;
            foreach ($fields as $field) {
                if ($rosterMember->$field != 0) {
                    $fieldIsNotZero = true;
                    break;
                }
            }

            if (! $fieldIsNotZero) {
                continue;
            }

            $memberName = $rosterMember->full_name.' '.$rosterMember->cid;
            $memberEmail = $rosterMember->user()->first()->email;
            $memberActivity = $rosterMember->currency;
            $requiredActivity = decimal_to_hm(config(sprintf('currency.%s', $rosterMember->status)));

            if ($rosterMember->visit == 1) {
                $badMembersVisit1[] = [
                    'name' => $memberName,
                    'email' => $memberEmail,
                    'activity' => decimal_to_hm($memberActivity),
                    'requirement' => $requiredActivity,
                ];
            } else {
                $badMembersVisit0[] = [
                    'name' => $memberName,
                    'email' => $memberEmail,
                    'activity' => decimal_to_hm($memberActivity),
                    'requirement' => $requiredActivity,
                ];
            }
        }

        // Fetch settings for email notifications
        $settings = CoreSettings::find(1);

        // Prepare the data to be sent in the email
        $data = [
            'visit0' => $badMembersVisit0,
            'visit1' => $badMembersVisit1,
        ];

        // Send email notification
        Notification::route('mail', [
            $settings->emailfirchief,
            $settings->emaildepfirchief,
            $settings->emailcinstructor,
        ])->notify(new MonthlyInactivity($data));

        // Reset the hours for every member
        DB::table('roster')->update(['currency' => 0]);

        // Remove our session logs because we don't need them anymore
        SessionLog::query()->truncate();

        return 0;
    }
}
