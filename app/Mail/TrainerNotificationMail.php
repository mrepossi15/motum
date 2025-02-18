<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Training;
use App\Models\User;

class TrainerNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainer;
    public $user;
    public $training;

    public function __construct(User $trainer, User $user, Training $training)
    {
        $this->trainer = $trainer;
        $this->user = $user;
        $this->training = $training;
    }

    public function build()
    {
        return $this->subject('¡Tienes una nueva venta en Motum!')
                    ->view('emails.trainer-notification');
    }
}