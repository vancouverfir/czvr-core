<?php

namespace App\Notifications\Feedback;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewControllerFeedback extends Notification
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
        $controller = \App\Models\Users\User::find($this->feedback->controller_cid);

        return (new MailMessage)
            ->subject('New Controller Feedback')
            ->view('emails.feedback.new', [
                'kind' => 'controller',
                'intro' => 'A user has submitted feedback on a Controller!',
                'details' => array_filter([
                    'Submitted by' => $this->feedback->user->fullName('FLC').' ('.$this->feedback->user->email.')',
                    'Controller' => $controller?->fullName('FLC') ?? $this->feedback->controller_cid,
                    'Position' => $this->feedback->position,
                ]),
                'content' => $this->feedback->content,
                'url' => route('staff.feedback.controller', $this->feedback->id),
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
