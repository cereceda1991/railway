<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $username;

    public function __construct($name)
    {
        $this->username = $name;

    }

    public function build()
    {
        return $this->subject('Welcome to CertifyMe')
                    ->view('emails.welcome');
    }
}
