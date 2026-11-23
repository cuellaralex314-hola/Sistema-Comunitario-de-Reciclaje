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
        Schema::create('puntos_recepcion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barrio_id')
                ->constrained('barrios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre', 100);
            $table->string('direccion', 150);
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('horario', 120)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['latitud', 'longitud']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_recepcion');
    }
};
