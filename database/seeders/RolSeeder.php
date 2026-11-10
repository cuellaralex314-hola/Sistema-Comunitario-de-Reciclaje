<?php



namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [Rol::ADMIN, 'admin', 'Administra usuarios, puntos, categorías y solicitudes'],
            [Rol::VECINO, 'vecino', 'Publica materiales y solicita retiros'],
            [Rol::RECOLECTOR, 'recolector', 'Recuperador o cooperativa que realiza los retiros'],
        ];

        foreach ($roles as [$id, $nombre, $descripcion]) {
            Rol::updateOrCreate(['id' => $id], ['nombre' => $nombre, 'descripcion' => $descripcion]);
        }
    }
}
