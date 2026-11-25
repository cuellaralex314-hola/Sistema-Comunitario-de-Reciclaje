<?php

namespace App\Models;

use Database\Factories\CategoriaResiduoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaResiduo extends Model
{
    /** @use HasFactory<CategoriaResiduoFactory> */
    use HasFactory;

    protected $table = 'categorias_residuo';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'instrucciones_preparacion',
        'que_no_va',
        'color_hex',
        'reciclable',
        'factor_co2_kg',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'reciclable' => 'boolean',
            'factor_co2_kg' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(ArticuloResiduo::class, 'categoria_id');
    }

    public function puntosRecepcion(): BelongsToMany
    {
        return $this->belongsToMany(
            PuntoRecepcion::class,
            'punto_categoria',
            'categoria_id',
            'punto_id',
        );
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }
}
