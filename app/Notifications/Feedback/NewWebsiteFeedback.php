<?php

namespace App\Notifications\Feedback;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewWebsiteFeedback extends Notification
{
    use Queueable;

    protected mixed $feedback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($feedback)
    {
        $this->feedback = $feedback;
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
        return (new MailMessage)
            ->subject('New Website Feedback')
            ->view('emails.feedback.new', [
                'kind' => 'website',
                'intro' => 'A USER has submitted feedback on the Website!',
                'details' => [
                    'Submitted by' => $this->feedback->user->fullName('FLC').' ('.$this->feedback->user->email.')',
                    'Subject' => $this->feedback->subject,
                ],
                'content' => $this->feedback->content,
                'url' => route('staff.feedback.website', $this->feedback->id),
            ]);
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
