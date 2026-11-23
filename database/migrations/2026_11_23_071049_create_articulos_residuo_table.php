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
        Schema::create('articulos_residuo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('categorias_residuo')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nombre', 100);
            $table->string('instrucciones', 255)->nullable();
            $table->boolean('reciclable')->default(true);
            $table->timestamps();

            $table->unique(['categoria_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos_residuo');
    }
};
