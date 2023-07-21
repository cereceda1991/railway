<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $url;

    public function __construct($publicKey)
    {
        $this->url = $_ENV['APP_URL_FRONTEND_CERTIFICATE'] . $publicKey;
    }

    public function build()
    {
        return $this->subject('Certificate Ready')
                    ->view('emails.certificate');
    }
}
