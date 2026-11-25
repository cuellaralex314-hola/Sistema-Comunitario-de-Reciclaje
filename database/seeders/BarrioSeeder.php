<?php

namespace Database\Seeders;

use App\Models\Barrio;
use Illuminate\Database\Seeder;

class BarrioSeeder extends Seeder
{
    public function run(): void
    {
        // Ejemplo: completar con el listado oficial de barrios de la ciudad
        $barrios = ['Centro', 'San Miguel', 'Don Bosco', 'Simón Bolívar', 'Guadalupe', 'Villa del Rosario'];

        foreach ($barrios as $nombre) {
            Barrio::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
