<?php

namespace App\Models;

use App\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Solicitud de retiro a domicilio.
 *
 * Asignables en masa: solo los datos que carga el vecino en el formulario.
 * vecino_id, recolector_id, estado, motivo_rechazo y completada_at los
 * maneja el sistema (relación del usuario y CambioEstadoSolicitudService).
 */
class SolicitudRetiro extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_retiro';

    protected $fillable = [
        'barrio_id', 'direccion', 'latitud', 'longitud', 'telefono_contacto',
        'fecha_preferida', 'franja_horaria', 'observaciones',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoSolicitud::class,
            'fecha_preferida' => 'date',
            'completada_at' => 'datetime',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
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
        return $this->belongsTo(Barrio::class);
    }

    public function materiales(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'solicitud_material', 'solicitud_id', 'material_id')
            ->withPivot('peso_real_kg');
    }

    /** Cambios de estado, del más viejo al más nuevo. */
    public function historial(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'solicitud_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    /** Solo las solicitudes asignadas a un recolector (T33). */
    public function scopeAsignadasA(Builder $query, User $recolector): Builder
    {
        return $query->where('recolector_id', $recolector->id);
    }
}
