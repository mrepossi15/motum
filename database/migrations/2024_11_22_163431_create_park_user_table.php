<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('park_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con entrenadores
            $table->foreignId('park_id')->constrained('parks')->onDelete('cascade'); // Relación con parques
            $table->timestamps();
        });
        
    }
    
    public function down()
    {
        Schema::dropIfExists('park_user');
    }
    
};
