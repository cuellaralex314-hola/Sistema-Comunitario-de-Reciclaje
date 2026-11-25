<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historial de cambios de estado de cada solicitud (T32).
 * Es un registro de auditoría: solo tiene created_at y nunca se modifica.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_retiro')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_nuevo', 20);
            $table->string('comentario', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['solicitud_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
    }
};
