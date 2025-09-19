<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data; // ['amount', 'payment_intent_id', 'timestamp']
    }

    public function build()
    {
        return $this->subject('(Cinemaglow ☆) New Donation Received - $' . $this->data['amount'])
            ->view('emails.payment_notification'); // custom blade view
    }
}
