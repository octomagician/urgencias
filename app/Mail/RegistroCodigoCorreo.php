<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegistroCodigoCorreo extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $contenido;
    public $verificationCode;

    public function __construct(User $user, string $contenido, $verificationCode)
    {
        $this->user = $user;
        $this->contenido = $contenido;
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->view('emails.RegistroCodigoCorreo')
                    ->with([
                        'user' => $this->user,
                        'contenido' => $this->contenido,
                        'verificationCode' => $this->verificationCode,
                    ]);
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Registro Correo',
        );
    }

    public function attachments()
    {
        return [];
    }
}