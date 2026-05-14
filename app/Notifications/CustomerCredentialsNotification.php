<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerCredentialsNotification extends Notification
{
    public function __construct(private readonly string $password, private readonly string $verifyToken) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Light House account credentials')
            ->view('emails.customer-credentials', [
                'name'        => $notifiable->name,
                'email'       => $notifiable->email,
                'password'    => $this->password,
                'verifyUrl'   => url('/email/verify/' . $this->verifyToken),
            ]);
    }
}
