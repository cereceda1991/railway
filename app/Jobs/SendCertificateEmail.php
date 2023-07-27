<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateEmail;

class SendCertificateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $publicKey;
    protected $email;
    protected $studentName; 

    /**
     * Create a new job instance.
     *
     * @param  string  $publicKey
     * @param  string  $email
     * @param  string  $studentName
     * @return void
     */
    public function __construct($publicKey, $email, $studentName)
    {
        $this->publicKey = $publicKey;
        $this->email = $email;
        $this->studentName = $studentName; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new CertificateEmail($this->publicKey, $this->studentName); 
        Mail::to($this->email)->send($mail);
    }
}
