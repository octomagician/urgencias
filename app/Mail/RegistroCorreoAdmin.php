<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegistroCorreoAdmin extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $contenido;
    public $signedUrl;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
        return $this->view('emails.RegistroCorreoAdmin')
                    ->with([
                        'user' => $this->user
                    ]);
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Nuevo usuario',
        );
    }

    public function attachments()
    {
        return [];
    }
}
