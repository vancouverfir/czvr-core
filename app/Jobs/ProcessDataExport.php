<?php

namespace App\Jobs;

use App\Models\Users\User;
use App\Notifications\DataExportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::whereId($this->user->id)->with(['notes', 'instructorProfile', 'studentProfile', 'staffProfile', 'rosterProfile', 'eventApplications', 'eventConfirms', 'tickets', 'ticketReplies'])->firstOrFail();
        $userArray = $user->toArray();
        $discord = null;
        if ($user->hasDiscord()) {
            $discord = $user->getDiscordUser();
        }
        $userArray['discord'] = $discord;
        $json = json_encode($userArray, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $user->notify(new DataExportRequest($user, $json));
    }
}
