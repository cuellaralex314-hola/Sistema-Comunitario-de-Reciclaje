<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Qué categorías de residuo acepta cada punto de recepción (muchos a muchos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punto_categoria', function (Blueprint $table) {
            $table->foreignId('punto_id')->constrained('puntos_recepcion')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias_residuo')->cascadeOnDelete();

            $table->primary(['punto_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punto_categoria');
    }
};
