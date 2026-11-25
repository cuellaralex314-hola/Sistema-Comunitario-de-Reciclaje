<?php

namespace App\Models;

use Database\Factories\HistorialEstadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstado extends Model
{
    /** @use HasFactory<HistorialEstadoFactory> */
    use HasFactory;

    protected $table = 'historial_estados';

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudRetiro::class, 'solicitud_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
