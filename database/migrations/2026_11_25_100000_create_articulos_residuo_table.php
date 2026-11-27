<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Objetos de la guía «¿Dónde va esto?» (por ejemplo, «Caja de pizza»).
 * Cada artículo pertenece a una categoría de residuo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos_residuo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_residuo')->restrictOnDelete();
            $table->string('nombre', 100)->unique();
            $table->string('instrucciones', 255)->nullable();
            $table->boolean('reciclable')->default(true);
            $table->timestamps();
        });

        // Índice FULLTEXT para el buscador (solo MySQL/MariaDB; SQLite no lo tiene).
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('articulos_residuo', function (Blueprint $table) {
                $table->fullText('nombre');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos_residuo');
    }
};
