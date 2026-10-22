<?php

namespace App\Notifications\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;

/**
 * For emails people receive because they subscribed (news, event reminders): a footer unsubscribe link and the
 * List-Unsubscribe headers, so mail apps can offer one-click unsubscribe.
 */
trait HasUnsubscribeLink
{
    protected function withUnsubscribeLink(MailMessage $message, object $notifiable): MailMessage
    {
        $url = $notifiable->emailUnsubscribeUrl();
        $message->viewData['unsubscribeUrl'] = $url;

        return $message->withSymfonyMessage(function (Email $email) use ($url): void {
            $email->getHeaders()->addTextHeader('List-Unsubscribe', '<'.$url.'>');
            $email->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });
    }
}
