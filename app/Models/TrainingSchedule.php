<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'day',
        'start_time',
        'end_time',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
    public function statuses()
{
    return $this->hasMany(TrainingStatus::class);
}
    
}
