<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Training;
use App\Models\User;

class TrainingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainer;
    public $training;

    public function __construct(User $trainer, Training $training)
    {
        $this->trainer = $trainer;
        $this->training = $training;
    }

    public function build()
    {
        return $this->subject('✅ Entrenamiento Creado Exitosamente')
                    ->view('emails.training_created')
                    ->with([
                        'trainerName' => $this->trainer->name,
                        'trainingTitle' => $this->training->title,
                        'parkName' => $this->training->park->name,
                        'activity' => $this->training->activity->name,
                        'schedule' => $this->training->schedules,
                    ]);
    }
}