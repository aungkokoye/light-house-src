<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your account has been activated')
            ->view('emails.account-activated', [
                'name'        => $notifiable->name,
                'isGoogleUser' => ! is_null($notifiable->google_id),
            ]);
    }
}
