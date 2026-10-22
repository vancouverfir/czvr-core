<?php

namespace App\Notifications;

use App\Models\AtcTraining\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalSuccessfulNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var list<int> */
    public array $backoff = [60, 300, 900, 3600];

    public function __construct(private readonly Student $student)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Training Request Has Been Renewed!')
            ->view('emails.renewalsuccessfulnotification', ['student' => $this->student]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
