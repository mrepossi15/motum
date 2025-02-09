<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['training_id', 'photo_path', 'training_photos_description'];

    // Relación con Training
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}