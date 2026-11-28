<?php

namespace App\Models;

use Database\Factories\PuntoRecepcionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PuntoRecepcion extends Model
{
    /** @use HasFactory<PuntoRecepcionFactory> */
    use HasFactory;

    protected $table = 'puntos_recepcion';

    protected $fillable = [
        'barrio_id',
        'nombre',
        'direccion',
        'latitud',
        'longitud',
        'horario',
        'telefono',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'activo' => 'boolean',
        ];
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoriaResiduo::class,
            'punto_categoria',
            'punto_id',
            'categoria_id',
        );
    }
}
