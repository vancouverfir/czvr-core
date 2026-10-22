<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasUnsubscribeLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class News extends Notification implements ShouldQueue
{
    use HasUnsubscribeLink, Queueable;

    protected mixed $user;

    protected mixed $news;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $news)
    {
        $this->user = $user;
        $this->news = $news;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail(object $notifiable): MailMessage
    {
        return $this->withUnsubscribeLink((new MailMessage)->view(
            'emails.news', ['user' => $this->user, 'news' => $this->news]
        )->subject('Vancouver FIR News | '.$this->news->title), $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
