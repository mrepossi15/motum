<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Training;
use App\Models\User;

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $training;

    public function __construct(User $user, Training $training)
    {
        $this->user = $user;
        $this->training = $training;
    }

    public function build()
    {
        return $this->subject('¡Compra Confirmada en Motum!')
                    ->view('emails.purchase-confirmation');
    }
}