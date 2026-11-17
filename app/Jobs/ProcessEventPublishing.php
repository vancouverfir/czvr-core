<?php

namespace App\Jobs;

use App\Models\Events\Event;
use App\Models\Users\User;
use App\Notifications\events\NewEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessEventPublishing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Event $event)
    {
    }

    public function handle(): void
    {
        $users = User::where('gdpr_subscribed_emails', 1)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        Notification::send($users, new NewEvent($this->event));
    }
}
