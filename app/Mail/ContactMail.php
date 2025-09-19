<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data; // ['name','email','phone','message']
    }

    public function build()
    {
        return $this->subject('(Cinemaglow ☆) Contact Message from '.$this->data['name'])
            ->replyTo($this->data['email'], $this->data['name']) // reply directly to sender
            ->view('emails.contact'); // custom blade view below
    }
}
