<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'user_id',         // Alumno que realizó el pago
        'training_id',     // Entrenamiento pagado
        'total_amount',    // Monto total pagado
        'company_fee',     // Comisión retenida por la empresa
        'trainer_amount',  // Monto transferido al entrenador
        'status',          // Estado del pago
        'payment_id',      // ID del pago en Mercado Pago
        'external_reference', // Referencia externa
        'weekly_sessions',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}