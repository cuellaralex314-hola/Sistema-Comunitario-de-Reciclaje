<?php

namespace Database\Factories;

use App\Enums\EstadoSolicitud;
use App\Models\Material;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitudRetiro>
 */
class SolicitudRetiroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vecino_id' => User::factory(),
            'estado' => EstadoSolicitud::Pendiente,
            'direccion' => fake()->streetAddress(),
            'telefono_contacto' => fake()->numerify('370-4######'),
            'fecha_preferida' => now()->addDays(3)->toDateString(),
            'franja_horaria' => 'mañana',
        ];
    }

    /** Ya asignada a un recolector (si no se pasa uno, se crea). */
    public function enProceso(?User $recolector = null): static
    {
        return $this->state(fn () => [
            'estado' => EstadoSolicitud::EnProceso,
            'recolector_id' => $recolector?->id ?? User::factory()->recolector(),
        ]);
    }

    /**
     * Le agrega materiales del mismo vecino, ya reservados
     * (como quedan después de crear la solicitud, T36).
     */
    public function conMateriales(int $cantidad = 2): static
    {
        return $this->afterCreating(function (SolicitudRetiro $solicitud) use ($cantidad) {
            $materiales = Material::factory()
                ->count($cantidad)
                ->reservado()
                ->create(['user_id' => $solicitud->vecino_id]);

            $solicitud->materiales()->attach($materiales->pluck('id'));
        });
    }
}
