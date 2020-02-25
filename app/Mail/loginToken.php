<?php

namespace App\Mail;

use App\login_token;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class loginToken extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(login_token $token)
    {
        $this->token = $token;
        $this->user = $this->token->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('irsandymsv98@gmail.com')
                    ->view('mail.login_token_mail')
                    ->subject('Permintaan Token Login');
    }
}
