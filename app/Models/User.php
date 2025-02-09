<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'certification',
        'certification_pic',
        'certification_pic_description',
        'biography',       // Nuevo campo
        'profile_pic',
        'profile_pic_description',
        'birth',
        'especialty',
        'mercado_pago_email',
        'collector_id', 
        'medical_fit'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
        public function isAdmin()
    {
        return $this->role === 'administrador';
    }

    public function isTrainer()
    {
        return $this->role === 'entrenador';
    }

    public function isStudent()
    {
        return $this->role === 'alumno';
    }

    public function parks()
    {
        return $this->belongsToMany(Park::class, 'park_user');
    }
    
    
    public function trainings()
    {
        return $this->hasMany(Training::class, 'trainer_id');
    }
    
    // Relación con las reseñas recibidas como entrenador
    public function reviews()
    {
        return $this->hasMany(Review::class, 'trainer_id');
    }
    public function trainingPhotos()
    {
        return $this->hasManyThrough(TrainingPhoto::class, Training::class, 'trainer_id', 'training_id', 'id', 'id');
    }
    public function experiences()
    {
        return $this->hasMany(UserExperience::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function purchasedTrainings()
    {
        return $this->belongsToMany(Training::class, 'payments', 'user_id', 'training_id');
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
        


}
