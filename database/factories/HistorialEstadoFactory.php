<?php

namespace Database\Factories;

use App\Models\HistorialEstado;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HistorialEstado>
 */
class HistorialEstadoFactory extends Factory
{
    protected $model = HistorialEstado::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solicitud_id' => SolicitudRetiro::factory(),
            'user_id' => User::factory(),
            'estado_anterior' => $this->faker->optional()->randomElement([
                'pendiente',
                'aceptada',
                'en_proceso',
                'completada',
                'rechazada',
                'cancelada',
            ]),
            'estado_nuevo' => $this->faker->randomElement([
                'pendiente',
                'aceptada',
                'en_proceso',
                'completada',
                'rechazada',
                'cancelada',
            ]),
            'comentario' => $this->faker->optional()->sentence(),
        ];
    }
}
