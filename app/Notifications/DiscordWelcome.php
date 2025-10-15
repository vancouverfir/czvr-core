<?php

namespace App\Notifications;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordWelcome extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * @param  $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return DiscordMessage::create('🎉 Hi ' . Auth::user()->fullName('F') . "! Welcome to the Vancouver FIR Discord server! 🎉\n\n" . "Welcome to the official Discord server of the Vancouver FIR! This space is primarily for FIR-wide interaction, sharing event details, OTS announcements, and important updates\n\n" . "Our rules are as follows:\n```\n" . "1. All VATSIM, VATCAN, and local FIR policies apply here\n" . "2. Usernames are automatically assigned by the FIR bot based on your display name, in accordance with our Privacy Policy https://czvr.ca/privacy-policy\n" . "3. NSFW content is strictly prohibited\n" . "4. Harassment of any kind will not be tolerated, including but not limited to racism, sexism, or hate speech\n" . "```\n" . "Failure to comply with these rules may result in removal from the server\n\n" . "Please use common sense and respect others—just like you would in any other community\n\n" . "If you have any questions, feel free to @FIR Staff — they’ll be happy to help!\n\n" . "Thanks for joining, and we look forward to working with you!");
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
