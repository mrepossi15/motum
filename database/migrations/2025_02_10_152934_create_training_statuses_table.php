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
        Schema::create('training_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_schedule_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Fecha específica de la clase suspendida
            $table->enum('status', ['active', 'suspended',])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_statuses');
    }
};
