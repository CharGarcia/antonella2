<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class InvitacionRegistro extends Mailable
{
    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Completa tu registro en CaMaGaRe')
            ->view('emails.invitacion-registro')
            ->with([
                'url' => url('/completar-registro/' . $this->token),
                'email' => $this->user->email,
            ]);
    }
}
