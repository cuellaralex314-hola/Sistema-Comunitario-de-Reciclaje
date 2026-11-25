<?php

namespace Database\Factories;

use App\Models\CategoriaResiduo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoriaResiduo>
 */
class CategoriaResiduoFactory extends Factory
{
    protected $model = CategoriaResiduo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(2, true),
            'slug' => $this->faker->unique()->slug(2),
            'descripcion' => $this->faker->optional()->sentence(),
            'instrucciones_preparacion' => $this->faker->optional()->sentence(),
            'que_no_va' => $this->faker->optional()->sentence(),
            'color_hex' => $this->faker->hexColor(),
            'reciclable' => $this->faker->boolean(80),
            'factor_co2_kg' => $this->faker->randomFloat(2, 0, 99.99),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
