<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario entrenador
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'marturepo@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'entrenador',
            'certification' => 'Personal Trainer Certificado',
            'certification_pic' => 'certifications/juan_perez.jpg',
            'certification_pic_description' => 'Certificación de entrenador personal.',
            'biography' => 'Apasionado del fitness y del entrenamiento funcional al aire libre.',
            'especialty' => 'Entrenamiento funcional y calistenia',
            'birth' => '1990-06-15',
            'profile_pic' => 'profiles/juan_perez.jpg',
            'profile_pic_description' => 'Foto de perfil de Juan Pérez.',
            'mercado_pago_email' => 'juan.mp@motum.com',
            'collector_id' => Str::random(10),
        ]);

        // Crear un usuario alumno
        User::create([
            'name' => 'María González',
            'email' => 'm@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'alumno',
            'biography' => 'Amante del deporte al aire libre y las actividades grupales.',
            'birth' => '1995-09-20',
            'profile_pic' => 'profiles/maria_gonzalez.jpg',
            'profile_pic_description' => 'Foto de perfil de María González.',
            'mercado_pago_email' => 'maria.mp@motum.com',
        ]);

        // Generar 5 entrenadores y 10 alumnos de manera aleatoria
        User::factory()->count(5)->create(['role' => 'entrenador']);
        User::factory()->count(10)->create(['role' => 'alumno']);
    }
}