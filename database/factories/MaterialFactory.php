<?php

namespace Database\Factories;

use App\Models\Barrio;
use App\Models\CategoriaResiduo;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'categoria_id' => CategoriaResiduo::factory(),
            'barrio_id' => Barrio::factory(),
            'descripcion' => $this->faker->sentence(),
            'cantidad' => $this->faker->randomFloat(2, 0.01, 999.99),
            'unidad' => $this->faker->randomElement(['kg', 'unidad', 'bolsa', 'caja']),
            'estado' => $this->faker->randomElement([
                'disponible',
                'reservado',
                'retirado',
                'cancelado',
            ]),
            'direccion_referencia' => $this->faker->optional()->streetAddress(),
            'latitud' => $this->faker->optional()->randomFloat(7, -90, 90),
            'longitud' => $this->faker->optional()->randomFloat(7, -180, 180),
            'foto_path' => $this->faker->optional()->filePath(),
        ];
    }
}
