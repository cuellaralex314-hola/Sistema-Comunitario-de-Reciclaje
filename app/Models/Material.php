<?php

namespace App\Models;

use Database\Factories\MaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    /** @use HasFactory<MaterialFactory> */
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'barrio_id',
        'descripcion',
        'cantidad',
        'unidad',
        'estado',
        'direccion_referencia',
        'latitud',
        'longitud',
        'foto_path',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaResiduo::class, 'categoria_id');
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }

    public function solicitudesRetiro(): BelongsToMany
    {
        return $this->belongsToMany(
            SolicitudRetiro::class,
            'solicitud_material',
            'material_id',
            'solicitud_id',
        )->withPivot('peso_real_kg');
    }
}
