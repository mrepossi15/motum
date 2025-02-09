<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Park extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'opening_hours',
        'location', // Nuevo campo
        'photo_urls',
    ];
    protected static function booted()
    {
        // Al crear o actualizar un entrenamiento
        static::saved(function ($training) {
            $park = $training->park; // Obtener el parque relacionado
            if ($park) {
                $park->searchable(); // Sincronizar el parque en Algolia
            }
        });

        // Al eliminar un entrenamiento
        static::deleted(function ($training) {
            $park = $training->park; // Obtener el parque relacionado
            if ($park) {
                $park->searchable(); // Sincronizar el parque en Algolia
            }
        });
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'park_user');
    }
    public function toSearchableArray()
    {
        $this->load('trainings.activity');

        return [
            'objectID' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            '_geoloc' => [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ],
            'activities' => $this->trainings
                ->map(fn($training) => $training->activity->name)
                ->unique()
                ->values()
                ->all(),
        ];
    }
    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function activities()
    {
        return $this->hasManyThrough(Activity::class, Training::class, 'park_id', 'id', 'id', 'activity_id')->distinct();
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}


