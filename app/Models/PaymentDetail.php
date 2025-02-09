<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id', // Relación con el pago principal
        'user_id',    // Usuario receptor (empresa o entrenador)
        'amount',     // Monto transferido
        'type',       // Tipo (e.g., empresa, entrenador)
    ];

    // Relaciones
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}