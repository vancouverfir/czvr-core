<?php

namespace App\Mail;

use App\Models\AtcTraining\InstructingSession as SessionModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstructingSession extends Mailable
{
    use Queueable, SerializesModels;

    public $session;
    public $action;

    protected function generateIcs()
    {
        $uid = 'AAA'.$this->session->id.'@vancouverfir.ca';

        $sequence = match ($this->action) {
            'created' => 0,
            'updated', 'cancelled' => $this->session->updated_at->timestamp,
            default => 0,
        };

        $start = $this->session->start_time->format('Ymd\THis\Z');
        $end = $this->session->end_time->format('Ymd\THis\Z');
        $title = $this->session->title;
        $description = 'Instructing session with '.$this->session->instructorUser()->fullName('FLC');

        $method = $this->action === 'cancelled' ? 'CANCEL' : 'REQUEST';

        $ics = <<<ICS
        BEGIN:VCALENDAR
        VERSION:2.0
        PRODID:-//Vancouver FIR//Instructing Session//EN
        METHOD:{$method}
        BEGIN:VEVENT
        UID:{$uid}
        SEQUENCE:{$sequence}
        DTSTAMP:{$start}
        DTSTART:{$start}
        DTEND:{$end}
        SUMMARY:{$title}
        DESCRIPTION:{$description}
        LOCATION:Vancouver FIR
        END:VEVENT
        END:VCALENDAR
        ICS;

        return $ics;
    }

    /**
     * Create a new message instance.
     *
     * @param  SessionModel  $session
     * @param  string  $action
     */
    public function __construct(SessionModel $session, string $action = 'created')
    {
        $this->session = $session;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = match ($this->action) {
            'created' => 'New Instructing Session Scheduled',
            'updated' => 'Instructing Session Updated',
            'cancelled' => 'Instructing Session Cancelled',
            default => 'Instructing Session Notification',
        };

        $recipient = $this->actionRecipient ?? 'instructor';
        $toEmail = $recipient === 'instructor'
            ? $this->session->instructorUser()->email
            : $this->session->student->user->email;

        $email = $this->to($toEmail)
                    ->subject($subject)
                    ->view("emails.instructingsession.{$this->action}")
                    ->with([
                        'session' => $this->session,
                        'recipient' => $recipient,
                    ]);

        if (in_array($this->action, ['created', 'updated', 'cancelled'])) {
            $email->attachData(
                $this->generateIcs(),
                'session.ics',
                ['mime' => 'text/calendar']
            );
        }

        return $email;
    }
}
