<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Orders\Models\Payment;

class PaymentReceiptNotification extends Notification
{
    public function __construct(private readonly Payment $payment) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Receipt — ' . $this->payment->invoice->invoice_no . ' from ' . env('VITE_COMPANY_NAME'))
            ->view('emails.payment-receipt', [
                'payment'         => $this->payment,
                'companyName'     => env('VITE_COMPANY_NAME',    ''),
                'companyAddress'  => env('VITE_COMPANY_ADDRESS', ''),
                'companyPhone'    => env('VITE_COMPANY_PHONE',   ''),
                'companyEmail'    => env('VITE_COMPANY_EMAIL',   ''),
                'companyFacebook' => env('VITE_COMPANY_FACEBOOK',''),
            ]);
    }
}
