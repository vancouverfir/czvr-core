<?php

namespace App\Mail;

use App\Models\AtcTraining\InstructingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SessionReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $session;

    public function __construct($recipient, InstructingSession $session)
    {
        $this->session = $session;

        $this->recipient = is_string($recipient)
            ? ($recipient === 'instructor'
                ? $this->session->instructorUser()
                : $this->session->student->user)
            : $recipient;
    }

    public function build()
    {
        return $this->to($this->recipient->email)
                    ->subject('Upcoming Instructing Session')
                    ->view('emails.instructingsession.reminder')
                    ->with([
                        'session' => $this->session,
                        'recipient' => $this->recipient,
                    ]);
    }
}
