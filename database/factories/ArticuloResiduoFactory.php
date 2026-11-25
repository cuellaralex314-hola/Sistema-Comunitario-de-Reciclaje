<?php

namespace Database\Factories;

use App\Models\ArticuloResiduo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArticuloResiduo>
 */
class ArticuloResiduoFactory extends Factory
{
    protected $model = ArticuloResiduo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categoria_id' => CategoriaResiduo::factory(),
            'nombre' => $this->faker->words(3, true),
            'instrucciones' => $this->faker->optional()->sentence(),
            'reciclable' => $this->faker->boolean(80),
        ];
    }
}
