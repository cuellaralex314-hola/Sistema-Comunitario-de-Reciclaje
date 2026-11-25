<?php

namespace Database\Factories;

use App\Models\Barrio;
use App\Models\PuntoRecepcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PuntoRecepcion>
 */
class PuntoRecepcionFactory extends Factory
{
    protected $model = PuntoRecepcion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'barrio_id' => Barrio::factory(),
            'nombre' => $this->faker->words(3, true),
            'direccion' => $this->faker->streetAddress(),
            'latitud' => $this->faker->randomFloat(7, -90, 90),
            'longitud' => $this->faker->randomFloat(7, -180, 180),
            'horario' => $this->faker->optional()->sentence(3),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
