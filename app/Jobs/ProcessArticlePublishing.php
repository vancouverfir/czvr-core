<?php

namespace App\Jobs;

use App\Models\News\News;
use App\Models\Users\User;
use App\Notifications\News as NewsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessArticlePublishing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected News $article)
    {
    }

    public function handle(): void
    {
        if ((int) $this->article->email_level !== News::EMAIL_SUBSCRIBERS) {
            return;
        }

        User::where('gdpr_subscribed_emails', 1)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $user->notify(new NewsNotification($user, $this->article));
                }
            });
    }
}
