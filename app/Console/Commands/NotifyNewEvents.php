<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEventPublishing;
use App\Models\Events\Event;
use Illuminate\Console\Command;

class NotifyNewEvents extends Command
{
    protected $signature = 'vancouver:events-notify-subscribers';

    protected $description = 'Email subscribers about newly added events';

    public function handle(): void
    {
        Event::whereNull('subscribers_notified_at')->each(function (Event $event): void {
            $claimed = Event::whereKey($event->id)
                ->whereNull('subscribers_notified_at')
                ->update(['subscribers_notified_at' => now()]);

            if ($claimed && $event->start_timestamp->isFuture()) {
                ProcessEventPublishing::dispatch($event);
            }
        });
    }
}
