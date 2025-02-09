<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $activities = [
            ['name' => 'Yoga'],
            ['name' => 'Funcional'],
            ['name' => 'Pilates'],
            ['name' => 'Running'],
            ['name' => 'Calistenia'],
            ['name' => 'Fútbol'],
            ['name' => 'Fútbol femenino'],
            ['name' => 'Zumba'],
            ['name' => 'Crossfit'],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
