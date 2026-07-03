<?php

namespace App\Notifications\network;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckVisitHours extends Notification
{
    use Queueable;

    private array $members;

    private array $unknown;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $members, array $unknown = [])
    {
        $this->members = $members;
        $this->unknown = $unknown;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.network.visiting', [
                'members' => $this->members,
                'unknown' => $this->unknown,
            ])
            ->subject($this->subject());
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }

    private function subject(): string
    {
        if (count($this->members) > 0) {
            return 'Controller Visiting Violations!';
        }

        if (count($this->unknown) > 0) {
            return 'Controller Visiting Check - VATSIM API Issues';
        }

        return 'Controller Visiting Check - No Violations';
    }
}
