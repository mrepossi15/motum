<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingPhotosTable extends Migration
{
    public function up(): void
    {
        Schema::create('training_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id');
            $table->string('photo_path'); // Ruta de la foto
            $table->string('training_photos_description')->nullable();
            $table->timestamps();

            // Llave foránea
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_photos');
    }
}