<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'usuarios.gestionar' => 'Alta, baja lógica y cambio de rol de usuarios',
            'puntos.gestionar' => 'Crear, editar y desactivar puntos de recepción',
            'categorias.gestionar' => 'Editar la guía de clasificación',
            'solicitudes.gestionar' => 'Ver todas las solicitudes, asignar y rechazar',
            'solicitudes.actualizar' => 'Cambiar el estado de solicitudes asignadas',
            'estadisticas.ver' => 'Acceder al panel de estadísticas',
            'materiales.publicar' => 'Publicar materiales y crear solicitudes',
        ];

        foreach ($permisos as $clave => $descripcion) {
            Permiso::updateOrCreate(['clave' => $clave], ['descripcion' => $descripcion]);
        }

        $ids = fn (array $claves) => Permiso::whereIn('clave', $claves)->pluck('id');

        Rol::findOrFail(Rol::ADMIN)->permisos()->sync($ids([
            'usuarios.gestionar', 'puntos.gestionar', 'categorias.gestionar',
            'solicitudes.gestionar', 'solicitudes.actualizar', 'estadisticas.ver',
        ]));
        Rol::findOrFail(Rol::VECINO)->permisos()->sync($ids(['materiales.publicar']));
        Rol::findOrFail(Rol::RECOLECTOR)->permisos()->sync($ids(['solicitudes.actualizar']));
    }
}
