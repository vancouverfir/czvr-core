<?php

namespace App\Console\Commands;

use App\Models\Events\Event;
use App\Notifications\events\EventReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Throwable;

class EventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send event reminders to controllers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $events = Event::where('start_timestamp', '<', Carbon::now()->addDay())
            ->where('start_timestamp', '>', Carbon::now())
            ->get();

        foreach ($events as $e) {
            $positionsByUser = $e->eventconfirm()
                ->with('user')
                ->get()
                ->groupBy('user_id');

            foreach ($positionsByUser as $userId => $positions) {
                $user = $positions->first()->user;

                if (! $user || $user->gdpr_subscribed_emails != 1) {
                    continue;
                }

                $cacheKey = "event-reminder:{$e->id}:{$userId}";

                if (! Cache::add($cacheKey, true, Carbon::parse($e->start_timestamp)->addDay())) {
                    continue;
                }

                try {
                    $user->notify(new EventReminder($e, $positions));
                } catch (Throwable $exception) {
                    Cache::forget($cacheKey);

                    throw $exception;
                }
            }
        }

        return 0;
    }
}
