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
    public $studentName; 

    public function __construct($publicKey, $studentName)
    {
        $this->url = $_ENV['APP_URL_FRONTEND_CERTIFICATE'] . $publicKey;
        $this->studentName = $studentName; 
    }

    public function build()
    {
        return $this->subject('Certificado Emitido ¡Felicidades!')
                    ->view('emails.certificate');
    }
}
