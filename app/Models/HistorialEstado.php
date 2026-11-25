<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Un paso en la vida de una solicitud (T32): de qué estado a cuál, quién y cuándo.
 * Es un registro de auditoría: se crea y no se modifica nunca.
 */
class HistorialEstado extends Model
{
    /** La tabla solo tiene created_at. */
    public const UPDATED_AT = null;

    protected $table = 'historial_estados';

    protected $fillable = ['user_id', 'estado_anterior', 'estado_nuevo', 'comentario'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('El historial de estados no se modifica.');
        });
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudRetiro::class, 'solicitud_id');
    }

    /** Quién hizo el cambio (null si el usuario ya no existe). */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
