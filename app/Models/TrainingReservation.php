<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingReservation extends Model {
    use HasFactory;

    protected $fillable = ['training_id', 'user_id', 'date', 'time','canceled_at', 'status'];

    public function training() {
        return $this->belongsTo(Training::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}