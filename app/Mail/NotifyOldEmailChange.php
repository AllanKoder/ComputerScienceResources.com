<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyOldEmailChange extends Mailable
{
    use Queueable, SerializesModels;

    public $newEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($newEmail)
    {
        $this->newEmail = $newEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.notify-old-email-change')
                    ->with(['newEmail' => $this->newEmail]);
    }
}
