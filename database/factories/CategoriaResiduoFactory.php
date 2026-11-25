<?php

namespace Database\Factories;

use App\Models\CategoriaResiduo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CategoriaResiduo>
 */
class CategoriaResiduoFactory extends Factory
{
    public function definition(): array
    {
        $nombre = ucfirst(fake()->unique()->words(2, true));

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre),
            'color_hex' => fake()->hexColor(),
            'reciclable' => true,
            'activo' => true,
        ];
    }
}
