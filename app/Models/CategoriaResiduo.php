<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaResiduo extends Model
{
    use HasFactory;

    protected $table = 'categorias_residuo';

    protected $fillable = [
        'nombre', 'slug', 'descripcion', 'instrucciones_preparacion', 'que_no_va',
        'color_hex', 'reciclable', 'factor_co2_kg', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'reciclable' => 'boolean',
            'activo' => 'boolean',
            'factor_co2_kg' => 'decimal:2',
        ];
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }
}
