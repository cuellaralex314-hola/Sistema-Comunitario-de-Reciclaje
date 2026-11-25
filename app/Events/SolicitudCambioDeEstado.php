<?php

namespace App\Events;

use App\Enums\EstadoSolicitud;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se dispara después de cada cambio de estado ya guardado (patrón Observer).
 *
 * Fabián (T38) escucha este evento para mandar la notificación al vecino,
 * sin tocar el servicio:
 *
 *   php artisan make:listener NotificarCambioDeEstado --event=SolicitudCambioDeEstado
 */
class SolicitudCambioDeEstado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SolicitudRetiro $solicitud,
        public ?EstadoSolicitud $anterior,
        public EstadoSolicitud $nuevo,
        public ?User $actor,
    ) {}
}
