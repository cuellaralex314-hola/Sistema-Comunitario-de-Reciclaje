<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Solicitudes de retiro a domicilio (T31).
 * El estado solo lo cambia App\Services\CambioEstadoSolicitudService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_retiro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vecino_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('recolector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('barrio_id')->nullable()->constrained('barrios')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'en_proceso', 'completada', 'cancelada', 'rechazada'])
                ->default('pendiente')
                ->index();
            $table->string('direccion', 150);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('telefono_contacto', 30);
            $table->date('fecha_preferida');
            $table->enum('franja_horaria', ['mañana', 'tarde']);
            $table->string('observaciones', 255)->nullable();
            $table->string('motivo_rechazo', 255)->nullable();
            $table->timestamp('completada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_retiro');
    }
};
