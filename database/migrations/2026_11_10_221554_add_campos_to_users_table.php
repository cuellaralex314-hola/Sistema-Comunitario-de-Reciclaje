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
       Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->after('id')->constrained('roles')->restrictOnDelete();
            $table->foreignId('barrio_id')->nullable()->after('rol_id')->constrained('barrios')->nullOnDelete();
            $table->string('apellido', 80)->nullable()->after('name');
            $table->string('telefono', 30)->nullable()->after('email');
            $table->string('direccion', 150)->nullable()->after('telefono');
            $table->boolean('activo')->default(true)->after('direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
            $table->dropConstrainedForeignId('barrio_id');
            $table->dropColumn(['apellido', 'telefono', 'direccion', 'activo']);
        });
    }
};
