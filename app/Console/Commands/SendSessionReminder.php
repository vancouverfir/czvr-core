<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AtcTraining\InstructingSession;
use Illuminate\Support\Facades\Mail;
use App\Mail\SessionReminder;
use Carbon\Carbon;

class SendSessionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:session-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send session reminder 3 hours before a session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $start = $now->copy()->addHours(3)->startOfMinute();
        $end   = $now->copy()->addHours(3)->endOfMinute();

        $sessions = InstructingSession::whereBetween('start_time', [$start, $end])->get();

        foreach ($sessions as $session) {
            $instructorUser = $session->instructorUser();
            Mail::to($instructorUser->email)->queue(new SessionReminder( $instructorUser, $session ));

            $studentUser = $session->student->user;
            Mail::to($studentUser->email)->queue(new SessionReminder( $studentUser, $session ));
        }
    }
}
