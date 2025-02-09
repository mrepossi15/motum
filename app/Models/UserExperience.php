<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'role','company', 'year_start','year_end','details'];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}