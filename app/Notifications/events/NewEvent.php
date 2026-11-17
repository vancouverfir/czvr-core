<?php

namespace App\Notifications\events;

use App\Models\Events\Event;
use App\Notifications\Concerns\HasUnsubscribeLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEvent extends Notification implements ShouldQueue
{
    use HasUnsubscribeLink, Queueable;

    public function __construct(protected Event $event)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return $this->withUnsubscribeLink((new MailMessage)->view(
            'emails.event.new', ['user' => $notifiable, 'event' => $this->event]
        )->subject('Vancouver FIR Event | '.$this->event->name), $notifiable);
    }
}
