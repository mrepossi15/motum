<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_schedule_id',
        'date',
        'status'
    ];

    // Relación con TrainingSchedule
    public function trainingSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class);
    }
}