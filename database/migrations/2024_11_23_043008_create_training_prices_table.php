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
        Schema::create('training_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->onDelete('cascade'); // Relación con entrenamientos
            $table->unsignedInteger('weekly_sessions'); // Veces por semana (ej: 2, 3, 4)
            $table->decimal('price', 10, 2); // Precio para esa cantidad de sesiones
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_prices');
    }
};
