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
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable(); // Rol o puesto
            $table->string('company')->nullable(); // Empresa o gimnasio
            $table->integer('year_start')->nullable(); // Año como número entero
            $table->integer('year_end')->nullable();
            $table->timestamps();
            // Relación con la tabla 'users'
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_experiences');
    }
};
