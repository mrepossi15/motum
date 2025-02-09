<?php

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassPurchasedByStudent extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Enviar por correo y guardar en base de datos
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva Clase Comprada')
            ->line('Un alumno ha comprado tu clase: ' . $this->payment->training->title)
            ->line('Sesiones por semana: ' . $this->payment->weekly_sessions)
            ->line('Monto recibido: $' . number_format($this->payment->trainer_amount, 2))
            ->action('Ver Detalles', url('/trainer/dashboard')) // Ajusta la URL a tu dashboard de entrenador
            ->line('¡Gracias por usar Motum!');
    }

    public function toArray($notifiable)
    {
        return [
            'training_title' => $this->payment->training->title,
            'student_name' => $this->payment->user->name,
            'weekly_sessions' => $this->payment->weekly_sessions,
            'amount_received' => $this->payment->trainer_amount,
        ];
    }
}