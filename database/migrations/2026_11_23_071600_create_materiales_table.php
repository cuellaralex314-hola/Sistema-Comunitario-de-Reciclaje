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
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('categoria_id')
                ->constrained('categorias_residuo')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('barrio_id')
                ->constrained('barrios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('descripcion', 255);
            $table->decimal('cantidad', 8, 2);

            $table->enum('unidad', [
                'kg',
                'unidad',
                'bolsa',
                'caja',
            ]);

            $table->enum('estado', [
                'disponible',
                'reservado',
                'retirado',
                'cancelado',
            ])->default('disponible');

            $table->string('direccion_referencia', 150)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();

            $table->index(['estado', 'categoria_id']);
            $table->index(['barrio_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
