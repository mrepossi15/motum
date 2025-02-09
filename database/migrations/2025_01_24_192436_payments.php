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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Alumno
            $table->foreignId('training_id')->constrained('trainings')->onDelete('cascade'); // Entrenamiento
            $table->decimal('total_amount', 10, 2); // Monto total pagado
            $table->decimal('company_fee', 10, 2); // Comisión de la empresa
            $table->decimal('trainer_amount', 10, 2); // Monto transferido al entrenador
            $table->string('status')->default('pending'); // Estado del pago (e.g., success, pending, failed)
            $table->string('payment_id')->nullable(); // ID del pago en Mercado Pago
            $table->string('external_reference')->nullable(); // Referencia externa
            $table->integer('weekly_sessions')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
