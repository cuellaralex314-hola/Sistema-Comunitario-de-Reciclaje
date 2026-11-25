<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Categorías de la guía de clasificación (Plástico, Vidrio, etc.).
 * Columnas según el DER (sección 9 del documento de diseño).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_residuo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->text('descripcion')->nullable();
            $table->text('instrucciones_preparacion')->nullable();
            $table->text('que_no_va')->nullable();
            $table->char('color_hex', 7);
            $table->boolean('reciclable')->default(true);
            $table->decimal('factor_co2_kg', 5, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_residuo');
    }
};
