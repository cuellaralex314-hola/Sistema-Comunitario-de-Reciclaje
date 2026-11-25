<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Solicitudes\AsignarRecolectorRequest;
use App\Http\Requests\Solicitudes\RechazarSolicitudRequest;
use App\Models\SolicitudRetiro;
use App\Models\User;
use App\Services\CambioEstadoSolicitudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Acciones del administrador sobre las solicitudes (T33 / HU13 / HU17).
 * El listado con filtros (T43) lo arma Alex sobre estas rutas.
 */
class SolicitudController extends Controller
{
    public function __construct(private CambioEstadoSolicitudService $estados) {}

    public function asignar(AsignarRecolectorRequest $request, SolicitudRetiro $solicitud): RedirectResponse
    {
        Gate::authorize('asignar', $solicitud);

        $recolector = User::findOrFail($request->validated('recolector_id'));
        $this->estados->asignar($solicitud, $recolector, $request->user());

        return back()->with('status', "Solicitud #{$solicitud->id} asignada a {$recolector->name}.");
    }

    public function rechazar(RechazarSolicitudRequest $request, SolicitudRetiro $solicitud): RedirectResponse
    {
        Gate::authorize('rechazar', $solicitud);

        $this->estados->rechazar($solicitud, $request->validated('motivo'), $request->user());

        return back()->with('status', "Solicitud #{$solicitud->id} rechazada.");
    }
}
