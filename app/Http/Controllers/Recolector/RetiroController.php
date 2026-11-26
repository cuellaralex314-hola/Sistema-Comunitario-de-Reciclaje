<?php

namespace App\Http\Controllers\Recolector;

use App\Enums\EstadoSolicitud;
use App\Http\Controllers\Controller;
use App\Http\Requests\Solicitudes\CompletarRetiroRequest;
use App\Models\SolicitudRetiro;
use App\Services\CambioEstadoSolicitudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Retiros asignados al recolector (T33 / HU17).
 */
class RetiroController extends Controller
{
    public function __construct(private CambioEstadoSolicitudService $estados) {}

    /**
     * Solo las solicitudes asignadas a quien inició sesión.
     * Primero las que faltan retirar, ordenadas por fecha.
     */
    public function index(Request $request): View
    {
        $retiros = SolicitudRetiro::query()
            ->asignadasA($request->user())
            ->with(['vecino:id,name,apellido', 'barrio:id,nombre', 'materiales.categoria:id,nombre'])
            ->orderByRaw('CASE WHEN estado = ? THEN 0 ELSE 1 END', [EstadoSolicitud::EnProceso->value])
            ->orderBy('fecha_preferida')
            ->paginate(15);

        return view('recolector.retiros.index', compact('retiros'));
    }

    public function completar(CompletarRetiroRequest $request, SolicitudRetiro $solicitud): RedirectResponse
    {
        Gate::authorize('completar', $solicitud);

        $this->estados->completar($solicitud, $request->validated('pesos'), $request->user());

        return redirect()
            ->route('recolector.retiros.index')
            ->with('status', "Retiro #{$solicitud->id} completado. ¡Gracias!");
    }
}
