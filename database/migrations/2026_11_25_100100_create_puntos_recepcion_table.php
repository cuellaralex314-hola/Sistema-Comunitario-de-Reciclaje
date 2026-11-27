<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Puntos verdes / centros donde los vecinos llevan sus residuos.
 * activo = false es la baja lógica: el punto no aparece en el mapa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntos_recepcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barrio_id')->nullable()->constrained('barrios')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('direccion', 150);
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->string('horario', 120)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_recepcion');
    }
};
