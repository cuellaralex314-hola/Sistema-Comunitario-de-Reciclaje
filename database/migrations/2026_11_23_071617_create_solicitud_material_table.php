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
        Schema::create('solicitud_material', function (Blueprint $table) {
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes_retiro')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained('materiales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('peso_real_kg', 8, 2)->nullable();

            $table->primary(['solicitud_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_material');
    }
};
