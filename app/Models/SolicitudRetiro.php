<?php

namespace App\Models;

use Database\Factories\SolicitudRetiroFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudRetiro extends Model
{
    /** @use HasFactory<SolicitudRetiroFactory> */
    use HasFactory;

    protected $table = 'solicitudes_retiro';

    protected $fillable = [
        'vecino_id',
        'recolector_id',
        'barrio_id',
        'estado',
        'direccion',
        'latitud',
        'longitud',
        'telefono_contacto',
        'fecha_preferida',
        'franja_horaria',
        'observaciones',
        'motivo_rechazo',
        'completada_at',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'fecha_preferida' => 'date',
            'completada_at' => 'datetime',
        ];
    }

    public function vecino(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vecino_id');
    }

    public function recolector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recolector_id');
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }

    public function materiales(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,
            'solicitud_material',
            'solicitud_id',
            'material_id',
        )->withPivot('peso_real_kg');
    }

    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'solicitud_id');
    }
}
