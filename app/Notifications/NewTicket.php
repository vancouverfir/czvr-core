<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicket extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($staff_member, $ticket)
    {
        $this->staff_member = $staff_member;
        $this->ticket = $ticket;
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
        return (new MailMessage)->view(
            'emails.newticket', ['user' => $this->staff_member->user, 'ticket' => $this->ticket]
        )->subject('#'.$this->ticket->ticket_id.' | '.$this->ticket->title);
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
