<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Milestone;
use App\Models\User;

class MilestoneSinAsignarMail extends Mailable
{
    use Queueable, SerializesModels;

    public $milestone;
    public $user;

    public function __construct(Milestone $milestone, User $user)
    {
        $this->milestone = $milestone;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Hay un encargo sin asignar en uno de tus proyectos en Aceler Project')
            ->view('emails.milestone_sin_asignar');
    }
}
