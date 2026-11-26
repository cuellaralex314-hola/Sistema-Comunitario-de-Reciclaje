<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Publicaciones de materiales de los vecinos.
 * Estado: disponible -> reservado -> retirado (o vuelve a disponible).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias_residuo')->restrictOnDelete();
            $table->foreignId('barrio_id')->nullable()->constrained('barrios')->nullOnDelete();
            $table->string('descripcion', 255);
            $table->decimal('cantidad', 8, 2);
            $table->enum('unidad', ['kg', 'unidades', 'bolsas']);
            $table->enum('estado', ['disponible', 'reservado', 'retirado', 'cancelado'])->default('disponible');
            $table->string('direccion_referencia', 150);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();

            // Acelera "materiales disponibles de tal categoría" y las estadísticas.
            $table->index(['estado', 'categoria_id']);
        });

        // Última barrera: la base rechaza cantidades en cero o negativas.
        // (SQLite no permite agregar CHECK después de crear la tabla.)
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE materiales ADD CONSTRAINT chk_materiales_cantidad CHECK (cantidad > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
