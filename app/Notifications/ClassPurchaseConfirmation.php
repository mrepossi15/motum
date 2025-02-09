<?php

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassPurchaseConfirmation extends Notification
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
            ->subject('Compra Confirmada')
            ->line('Has comprado la clase: ' . $this->payment->training->title)
            ->line('Sesiones por semana: ' . $this->payment->weekly_sessions)
            ->line('Monto pagado: $' . number_format($this->payment->amount, 2))
            ->action('Ver Mis Clases', url('/my/classes')) // Ajusta la URL a tus clases
            ->line('¡Gracias por elegir Motum!');
    }

    public function toArray($notifiable)
    {
        return [
            'training_title' => $this->payment->training->title,
            'amount_paid' => $this->payment->amount,
            'weekly_sessions' => $this->payment->weekly_sessions,
        ];
    }
}