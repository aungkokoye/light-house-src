<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactInquiryNotification extends Notification
{
    public function __construct(
        private readonly string $name,
        private readonly string $contact,
        private readonly string $service,
        private readonly string $message,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Inquiry from {$this->name}")
            ->view('emails.contact-inquiry', [
                'name'    => $this->name,
                'contact' => $this->contact,
                'service' => $this->service,
                'body'    => $this->message,
            ]);
    }
}