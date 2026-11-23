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
        Schema::create('punto_categoria', function (Blueprint $table) {
            $table->foreignId('punto_id')
                ->constrained('puntos_recepcion')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('categoria_id')
                ->constrained('categorias_residuo')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->primary(['punto_id', 'categoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punto_categoria');
    }
};
