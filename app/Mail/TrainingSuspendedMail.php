<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Training;

class TrainingSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $training;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct(Training $training, $date)
    {
        $this->training = $training;
        $this->date = $date;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Motum - Clase Suspendida')
                    ->view('emails.training-suspended');
    }
}