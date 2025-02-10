<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('training_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->onDelete('cascade'); // Entrenamiento al que pertenece la reserva
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Alumno que reserva
            $table->date('date'); // Fecha específica de la clase reservada
            $table->time('time'); // Hora específica de la clase reservada
            $table->timestamp('canceled_at')->nullable();
            $table->enum('status', ['active', 'completed', 'no-show'])->default('active');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('training_reservations');
    }
};