<?php

namespace App\Models;

use Database\Factories\ArticuloResiduoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticuloResiduo extends Model
{
    /** @use HasFactory<ArticuloResiduoFactory> */
    use HasFactory;

    protected $table = 'articulos_residuo';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'instrucciones',
        'reciclable',
    ];

    protected function casts(): array
    {
        return [
            'reciclable' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaResiduo::class, 'categoria_id');
    }
}
