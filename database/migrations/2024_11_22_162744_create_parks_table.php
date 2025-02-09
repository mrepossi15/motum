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
    Schema::create('parks', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nombre del parque
        $table->string('location');
        $table->decimal('latitude', 10, 7); // Latitud
        $table->decimal('longitude', 10, 7); // Longitud
        $table->text('opening_hours')->nullable(); // Horarios de apertura
        $table->json('photo_urls')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('parks');
}

};
