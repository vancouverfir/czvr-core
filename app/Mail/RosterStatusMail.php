<?php

namespace App\Mail;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RosterStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $controller;

    public $user;

    public function __construct(RosterMember $controller, User $user)
    {
        $this->controller = $controller;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.rosterstatus')
            ->subject($this->controller->active == 1
                ? "You're active on the Vancouver FIR roster again"
                : "You've been marked inactive on the Vancouver FIR roster");
    }
}
