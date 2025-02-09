<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPrice extends Model
{
    use HasFactory;

    protected $fillable = ['training_id', 'weekly_sessions', 'price'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}

