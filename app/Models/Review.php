<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{use HasFactory;

    protected $fillable = [
        'user_id',
        'trainer_id',
        'training_id',
        'comment',
        'rating',
    ];

    // Relación con el usuario que deja la reseña
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el entrenador reseñado
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Relación con el entrenamiento reseñado
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}