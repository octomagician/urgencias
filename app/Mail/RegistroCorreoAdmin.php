<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class RegistroCorreoAdmin extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $contenido;
    public $signedUrl;
    
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->authorizationUrl = URL::temporarySignedRoute(
            'authorize.user.role',
            Carbon::now()->addMinutes(5),
            ['user' => $this->user->id]
        );
    }
    
    public function build()
    {
        return $this->view('emails.RegistroCorreoAdmin')
                    ->subject("Solicitud de Autorización de Rol")
                    ->with([
                        'user' => $this->user,
                        'authorizationUrl' => $this->authorizationUrl,
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
