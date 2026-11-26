<?php

namespace Database\Factories;

use App\Models\CategoriaResiduo;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'categoria_id' => CategoriaResiduo::factory(),
            'descripcion' => fake()->sentence(4),
            'cantidad' => fake()->randomFloat(2, 0.5, 20),
            'unidad' => 'kg',
            'estado' => Material::DISPONIBLE,
            'direccion_referencia' => fake()->streetAddress(),
        ];
    }

    public function reservado(): static
    {
        return $this->state(fn () => ['estado' => Material::RESERVADO]);
    }
}
