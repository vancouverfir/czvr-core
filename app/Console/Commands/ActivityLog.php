<?php

namespace App\Console\Commands;

use App\Classes\VatsimHelper;
use App\Mail\ActivityBot\UnauthorisedConnection;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\MonitoredPosition;
use App\Models\Network\SessionLog;
use App\Models\Settings\CoreSettings;
use App\Services\TrainingProgressionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ActivityLog extends Command
{
    private const LOCAL_PREFIXES = [
        'CZVR_', 'ZVR_', 'CYVR_', 'CYYJ_', 'CYXS_', 'CYLW_', 'CYXX_', 'CZBB_', 'CYCD_', 'CYAZ_',
        'CYQQ_', 'CYZT_', 'CYXC_', 'CYCG_', 'CYHC_', 'CYNJ_', 'CYPK_', 'CYKA_', 'CYZP_', 'CYYD_',
        'CYXT_', 'CYWL_', 'CYDC_', 'CYBL_', 'CYWH_', 'CYQZ_',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs activity logger';

    /**
     * Execute the console command.
     */
    public function handle(TrainingProgressionService $progression): int
    {
        $payload = VatsimHelper::getDatafeed();
        if ($payload === null) {
            return Command::SUCCESS;
        }

        $positions = MonitoredPosition::all()->keyBy('identifier');
        $online = collect($payload['controllers'])
            ->filter(fn ($controller) => is_array($controller)
                && isset($controller['callsign'], $controller['cid'], $controller['logon_time'], $controller['facility']))
            ->filter(fn (array $controller) => $positions->has($controller['callsign'])
                || ((int) $controller['facility'] !== 0
                    && ! Str::endsWith($controller['callsign'], ['ATIS', 'OBS'])
                    && Str::startsWith($controller['callsign'], self::LOCAL_PREFIXES)))
            ->keyBy(fn (array $controller) => $this->sessionKey(
                (int) $controller['cid'], $controller['callsign'], Carbon::parse($controller['logon_time']),
            ));

        $openSessions = SessionLog::whereNull('session_end')->get();
        $roster = RosterMember::whereIntegerInRaw('cid', $online->pluck('cid')->merge($openSessions->pluck('cid')))
            ->get()
            ->keyBy('cid');
        $openKeys = $openSessions->keyBy(fn (SessionLog $log) => $this->sessionKey(
            (int) $log->cid, $log->callsign ?? '', $log->session_start,
        ));

        foreach ($online as $key => $controller) {
            $callsign = $controller['callsign'];
            $cid = (int) $controller['cid'];
            $position = $positions->get($callsign) ?? MonitoredPosition::firstOrCreate(['identifier' => $callsign]);
            $positions->put($callsign, $position);
            $rosterMember = $roster->get($cid);

            if (! $rosterMember) {
                $this->notifyUnauthorized($cid, $callsign);
            }

            if (! $openKeys->has($key)) {
                SessionLog::create([
                    'callsign' => $callsign,
                    'cid' => $cid,
                    'roster_member_id' => $rosterMember?->id,
                    'session_start' => Carbon::parse($controller['logon_time']),
                    'monitored_position_id' => $position->id,
                    'emails_sent' => 0,
                ]);
            }
        }

        foreach ($openSessions as $log) {
            if ($online->has($this->sessionKey((int) $log->cid, $log->callsign ?? '', $log->session_start))) {
                continue;
            }

            DB::transaction(function () use ($log, $progression): void {
                $end = now();
                $hours = max(0, $log->session_start->diffInMinutes($end) / 60);

                if (! SessionLog::whereKey($log->id)->whereNull('session_end')
                    ->update(['session_end' => $end, 'duration' => $hours])) {
                    return;
                }

                $member = RosterMember::where('cid', $log->cid)->lockForUpdate()->first();
                if (! $member?->active || ! in_array($member->status, ['home', 'instructor', 'visit', 'training'], true)) {
                    return;
                }

                $member->currency += $hours;
                if ($progression->sessionCountsTowardRatingHours($member, $log->callsign ?? '')) {
                    $member->rating_hours += $hours;
                }
                $member->save();
                $progression->completeConsolidationIfEligible($member);
            });
        }

        return Command::SUCCESS;
    }

    private function sessionKey(int $cid, string $callsign, \DateTimeInterface $start): string
    {
        return $cid.'|'.$callsign.'|'.$start->getTimestamp();
    }

    private function notifyUnauthorized(int $cid, string $callsign): void
    {
        if (! Cache::add('unauth_email_sent_'.$cid.'_'.$callsign, true, now()->addMinutes(15))) {
            return;
        }

        $settings = CoreSettings::find(1);
        foreach (array_filter([$settings?->emailfirchief, $settings?->emaildepfirchief, $settings?->emailcinstructor]) as $email) {
            Mail::to($email)->send(new UnauthorisedConnection([
                'callsign' => $callsign,
                'cid' => $cid,
            ]));
        }
    }
}
