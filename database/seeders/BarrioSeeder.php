<?php

namespace Database\Seeders;

use App\Models\Barrio;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class BarrioSeeder extends Seeder
{
    public function run(): void
    {
        // Contraseña de todos: "password" (el cast 'hashed' del modelo la cifra)
        $usuarios = [
            ['admin@reciclaje.test', 'Admin', 'Sistema', Rol::ADMIN, 'Centro'],
            ['laura@reciclaje.test', 'Laura', 'Benítez', Rol::VECINO, 'San Miguel'],
            ['diego@reciclaje.test', 'Diego', 'Acosta', Rol::VECINO, 'Don Bosco'],
            ['coop@reciclaje.test', 'Coop.', 'Reciclar', Rol::RECOLECTOR, 'Centro'],
        ];

        foreach ($usuarios as [$email, $nombre, $apellido, $rol, $barrio]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $nombre,
                'apellido' => $apellido,
                'password' => 'password',
                'rol_id' => $rol,
                'barrio_id' => Barrio::where('nombre', $barrio)->value('id'),
            ]);
        }
    }
}