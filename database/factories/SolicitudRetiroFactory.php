<?php

namespace Database\Factories;

use App\Models\Barrio;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitudRetiro>
 */
class SolicitudRetiroFactory extends Factory
{
    protected $model = SolicitudRetiro::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vecino_id' => User::factory(),
            'recolector_id' => null,
            'barrio_id' => Barrio::factory(),
            'estado' => 'pendiente',
            'direccion' => $this->faker->streetAddress(),
            'latitud' => $this->faker->optional()->randomFloat(7, -90, 90),
            'longitud' => $this->faker->optional()->randomFloat(7, -180, 180),
            'telefono_contacto' => $this->faker->phoneNumber(),
            'fecha_preferida' => $this->faker->dateTimeBetween('today', '+30 days'),
            'franja_horaria' => $this->faker->randomElement(['manana', 'tarde', 'noche']),
            'observaciones' => $this->faker->optional()->sentence(),
            'motivo_rechazo' => null,
            'completada_at' => null,
        ];
    }
}
