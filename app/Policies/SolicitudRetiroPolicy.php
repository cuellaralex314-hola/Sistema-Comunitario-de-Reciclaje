<?php

namespace App\Policies;

use App\Models\SolicitudRetiro;
use App\Models\User;

/**
 * Quién puede hacer qué con una solicitud de retiro (T33 / HU9 / HU17).
 *
 * La policy responde "¿esta persona puede?". Si el estado lo permite lo
 * decide CambioEstadoSolicitudService. Uso en controladores:
 *
 *   Gate::authorize('completar', $solicitud);
 *
 * y en vistas:  @can('cancelar', $solicitud) ... @endcan
 */
class SolicitudRetiroPolicy
{
    /** Ver el detalle: el admin, el vecino que la pidió o el recolector asignado. */
    public function view(User $user, SolicitudRetiro $solicitud): bool
    {
        return $user->tienePermiso('solicitudes.gestionar')
            || $this->esElVecino($user, $solicitud)
            || $this->esElRecolector($user, $solicitud);
    }

    /** Asignar un recolector: solo quien gestiona solicitudes (admin). */
    public function asignar(User $user, SolicitudRetiro $solicitud): bool
    {
        return $user->tienePermiso('solicitudes.gestionar');
    }

    /** Rechazar: solo quien gestiona solicitudes (admin). */
    public function rechazar(User $user, SolicitudRetiro $solicitud): bool
    {
        return $user->tienePermiso('solicitudes.gestionar');
    }

    /** Cancelar: solo el vecino que la pidió. */
    public function cancelar(User $user, SolicitudRetiro $solicitud): bool
    {
        return $this->esElVecino($user, $solicitud);
    }

    /**
     * Completar: el recolector asignado. El admin también puede, para cubrir
     * el caso en que el recolector no tenga acceso (plan de recorte del backlog).
     */
    public function completar(User $user, SolicitudRetiro $solicitud): bool
    {
        if ($user->tienePermiso('solicitudes.gestionar')) {
            return true;
        }

        return $user->tienePermiso('solicitudes.actualizar')
            && $this->esElRecolector($user, $solicitud);
    }

    private function esElVecino(User $user, SolicitudRetiro $solicitud): bool
    {
        return (int) $solicitud->vecino_id === (int) $user->id;
    }

    private function esElRecolector(User $user, SolicitudRetiro $solicitud): bool
    {
        return $solicitud->recolector_id !== null
            && (int) $solicitud->recolector_id === (int) $user->id;
    }
}
