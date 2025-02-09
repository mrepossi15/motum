<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Activity extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'name',
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
   
    public function parks()
    {
        return $this->hasManyThrough(Park::class, Training::class, 'activity_id', 'id', 'id', 'park_id');
    }
}

