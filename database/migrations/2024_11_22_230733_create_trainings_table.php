<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade'); // Entrenador
            $table->foreignId('park_id')->constrained('parks')->onDelete('cascade'); // Parque
            $table->string('title'); // Título del entrenamiento
            $table->text('description')->nullable(); // Descripción opcional
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->enum('level', ['Principiante', 'Intermedio', 'Avanzado'])->default('Principiante');
            $table->integer('available_spots')->default(15); // Número máximo de participantes
            $table->timestamps(); // created_at y updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
