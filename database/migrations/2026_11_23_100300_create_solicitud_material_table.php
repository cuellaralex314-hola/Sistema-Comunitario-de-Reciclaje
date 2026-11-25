<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Qué materiales incluye cada solicitud y cuánto pesaron al retirarlos.
 * peso_real_kg es la fuente de las estadísticas (se carga al completar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_material', function (Blueprint $table) {
            $table->foreignId('solicitud_id')->constrained('solicitudes_retiro')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materiales')->restrictOnDelete();
            $table->decimal('peso_real_kg', 8, 2)->nullable();

            $table->primary(['solicitud_id', 'material_id']);
        });

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE solicitud_material ADD CONSTRAINT chk_solicitud_material_peso CHECK (peso_real_kg >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_material');
    }
};
