<?php

namespace App\Notifications;

use App\Models\AtcTraining\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalReminderNotification extends Notification implements ShouldQueue
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
            ->subject('Reminder: Renew Your Training Request')
            ->view('emails.renewalremindernotification', [
                'student' => $this->student,
                'renewalLink' => route('training.renew', $this->student->renewal_token),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
