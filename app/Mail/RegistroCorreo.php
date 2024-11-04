<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegistroCorreo extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $contenido;
    public $signedUrl;
    
    public function __construct(User $user, string $contenido, $signedUrl)
    {
        $this->user = $user;
        $this->contenido = $contenido;
        $this->signedUrl = $signedUrl;
    }
    
    public function build()
    {
        return $this->view('emails.RegistroCorreo')
                    ->with([
                        'user' => $this->user,
                        'contenido' => $this->contenido,
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
