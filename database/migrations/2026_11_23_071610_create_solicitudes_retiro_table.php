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
        Schema::create('solicitudes_retiro', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vecino_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('recolector_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('barrio_id')
                ->constrained('barrios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('estado', [
                'pendiente',
                'aceptada',
                'en_proceso',
                'completada',
                'rechazada',
                'cancelada',
            ])->default('pendiente');

            $table->string('direccion', 150);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('telefono_contacto', 30);
            $table->date('fecha_preferida');

            $table->enum('franja_horaria', [
                'manana',
                'tarde',
                'noche',
            ]);

            $table->string('observaciones', 255)->nullable();
            $table->string('motivo_rechazo', 255)->nullable();
            $table->timestamp('completada_at')->nullable();
            $table->timestamps();

            $table->index(['estado', 'fecha_preferida']);
            $table->index(['recolector_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_retiro');
    }
};
