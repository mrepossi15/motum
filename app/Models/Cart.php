<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'training_id',
        'weekly_sessions',
    ];
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
