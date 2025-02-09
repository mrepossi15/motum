<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'park_id',
        'title',
        'description',
        'activity_id',
        'available_spots',
        'level',
    ];

    // Relación con el entrenador
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Relación con el parque
    public function park()
    {
        return $this->belongsTo(Park::class);
    }

    // Relación con la actividad
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    // Relación con horarios de entrenamiento
    public function schedules()
    {
        return $this->hasMany(TrainingSchedule::class);
    }

    // Relación con precios de entrenamiento
    public function prices()
    {
        return $this->hasMany(TrainingPrice::class);
    }

    /**
     * Eventos de Eloquent para sincronizar Algolia.
     */
    protected static function booted()
    {
        // Al guardar un entrenamiento (crear o actualizar)
        static::saved(function ($training) {
            $park = $training->park; // Obtener el parque relacionado
            if ($park) {
                $park->searchable(); // Sincronizar el parque con Algolia
            }
        });

        // Al eliminar un entrenamiento
        static::deleted(function ($training) {
            $park = $training->park; // Obtener el parque relacionado
            if ($park) {
                $park->searchable(); // Sincronizar el parque con Algolia
            }
        });
    }
    // Relación con las reseñas del entrenamiento
    public function reviews()
    {
        return $this->hasMany(Review::class, 'training_id');
    }

    public function photos()
    {
        return $this->hasMany(TrainingPhoto::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function reservations() {
        return $this->hasMany(TrainingReservation::class);
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'payments', 'training_id', 'user_id');
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
 
}
