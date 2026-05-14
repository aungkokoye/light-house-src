<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Orders\Models\Invoice;

class InvoiceNotification extends Notification
{
    public function __construct(private readonly Invoice $invoice) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice ' . $this->invoice->invoice_no . ' from ' . env('VITE_COMPANY_NAME'))
            ->view('emails.invoice', [
                'invoice'         => $this->invoice,
                'companyName'     => env('VITE_COMPANY_NAME'),
                'companyAddress'  => env('VITE_COMPANY_ADDRESS'),
                'companyPhone'    => env('VITE_COMPANY_PHONE'),
                'companyEmail'    => env('VITE_COMPANY_EMAIL'),
                'companyFacebook' => env('VITE_COMPANY_FACEBOOK'),
            ]);
    }
}
