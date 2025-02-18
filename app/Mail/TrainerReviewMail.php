<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Review;
use App\Models\User;

class TrainerReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;
    public $trainer;

    /**
     * Create a new message instance.
     */
    public function __construct(Review $review, User $trainer)
    {
        $this->review = $review;
        $this->trainer = $trainer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Motum - Has recibido una nueva reseña')
                    ->view('emails.trainer-review');
    }
}