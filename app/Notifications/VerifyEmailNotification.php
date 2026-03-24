<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    public function __construct(private readonly string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url('/email/verify/' . $this->token);

        return (new MailMessage)
            ->subject('Verify your email address')
            ->view('emails.verify-email', [
                'url'  => $url,
                'name' => $notifiable->name,
            ]);
    }
}