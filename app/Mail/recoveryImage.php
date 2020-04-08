<?php

namespace App\Mail;

// use App\recovery_image;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class recoveryImage extends Mailable
{
    use Queueable, SerializesModels;

    public $reset_code;
    public $nama_user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $nama)
    {
        $this->reset_code = $code;
        $this->nama_user = $nama;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.recovery_gambar_mail')->subject('Permintaan Recovery Gambar');
    }
}
