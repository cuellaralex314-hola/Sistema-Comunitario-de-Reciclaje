<?php

namespace App\Services;

use App\Enums\EstadoSolicitud;
use App\Events\SolicitudCambioDeEstado;
use App\Exceptions\TransicionInvalidaException;
use App\Models\HistorialEstado;
use App\Models\Material;
use App\Models\Rol;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Único lugar donde cambia el estado de una solicitud de retiro (T31, T32).
 *
 * Cada cambio:
 *   1. se hace dentro de una transacción y con la fila bloqueada
 *      (dos personas no pueden cambiar la misma solicitud a la vez);
 *   2. se valida contra las transiciones de EstadoSolicitud;
 *   3. actualiza el estado de los materiales incluidos;
 *   4. queda en historial_estados con quién y cuándo (T32);
 *   5. dispara SolicitudCambioDeEstado cuando ya se guardó (avisos, T38).
 *
 * Si algo falla en el medio, la transacción deshace todo: nunca queda una
 * solicitud completada con materiales sin pesar, ni un cambio sin historial.
 *
 * Quién puede hacer cada acción lo decide SolicitudRetiroPolicy;
 * este servicio decide CUÁNDO se puede (según el estado).
 */
class CambioEstadoSolicitudService
{
    /**
     * Primer registro del historial (estado_anterior = null).
     * Fabián lo llama en T36, dentro de la misma transacción en la que crea la solicitud.
     */
    public function registrarCreacion(SolicitudRetiro $solicitud, User $vecino): HistorialEstado
    {
        $registro = $solicitud->historial()->create([
            'user_id' => $vecino->id,
            'estado_anterior' => null,
            'estado_nuevo' => EstadoSolicitud::Pendiente->value,
            'comentario' => 'Solicitud creada',
        ]);

        SolicitudCambioDeEstado::dispatch($solicitud, null, EstadoSolicitud::Pendiente, $vecino);

        return $registro;
    }

    /**
     * pendiente -> en_proceso. El administrador asigna un recolector activo.
     */
    public function asignar(SolicitudRetiro $solicitud, User $recolector, User $actor): SolicitudRetiro
    {
        if ((int) $recolector->rol_id !== Rol::RECOLECTOR || ! $recolector->activo) {
            throw new TransicionInvalidaException('Solo se puede asignar a un recolector con la cuenta activa.');
        }

        return $this->transicionar(
            $solicitud,
            EstadoSolicitud::EnProceso,
            $actor,
            function (SolicitudRetiro $s) use ($recolector) {
                $s->recolector_id = $recolector->id;
            },
            "Asignada a {$recolector->name}",
        );
    }

    /**
     * en_proceso -> completada. Se carga el peso real de CADA material
     * de la solicitud y los materiales pasan a retirados.
     *
     * @param  array<int|string, int|float|string>  $pesos  [material_id => kg]
     */
    public function completar(SolicitudRetiro $solicitud, array $pesos, User $actor): SolicitudRetiro
    {
        return $this->transicionar(
            $solicitud,
            EstadoSolicitud::Completada,
            $actor,
            function (SolicitudRetiro $s) use ($pesos) {
                $idsDeLaSolicitud = $s->materiales()->pluck('materiales.id')->map(fn ($id) => (int) $id);
                $idsRecibidos = collect(array_keys($pesos))->map(fn ($id) => (int) $id);

                if ($idsDeLaSolicitud->sort()->values()->all() !== $idsRecibidos->sort()->values()->all()) {
                    throw new TransicionInvalidaException('Cargá el peso de todos los materiales de la solicitud, y solo de esos.');
                }

                foreach ($pesos as $materialId => $kg) {
                    if (! is_numeric($kg) || $kg < 0) {
                        throw new TransicionInvalidaException('Los pesos tienen que ser números mayores o iguales a 0.');
                    }

                    $s->materiales()->updateExistingPivot((int) $materialId, [
                        'peso_real_kg' => round((float) $kg, 2),
                    ]);
                }

                $s->completada_at = now();
                $this->cambiarEstadoDeMateriales($s, Material::RETIRADO);
            },
        );
    }

    /**
     * pendiente -> cancelada. Lo hace el vecino; sus materiales vuelven a estar disponibles.
     */
    public function cancelar(SolicitudRetiro $solicitud, User $actor): SolicitudRetiro
    {
        return $this->transicionar(
            $solicitud,
            EstadoSolicitud::Cancelada,
            $actor,
            fn (SolicitudRetiro $s) => $this->cambiarEstadoDeMateriales($s, Material::DISPONIBLE),
        );
    }

    /**
     * pendiente o en_proceso -> rechazada, con motivo obligatorio.
     * Los materiales vuelven a estar disponibles.
     */
    public function rechazar(SolicitudRetiro $solicitud, string $motivo, User $actor): SolicitudRetiro
    {
        $motivo = trim($motivo);

        if ($motivo === '') {
            throw new TransicionInvalidaException('Para rechazar una solicitud hay que indicar el motivo.');
        }

        return $this->transicionar(
            $solicitud,
            EstadoSolicitud::Rechazada,
            $actor,
            function (SolicitudRetiro $s) use ($motivo) {
                $s->motivo_rechazo = $motivo;
                $this->cambiarEstadoDeMateriales($s, Material::DISPONIBLE);
            },
            $motivo,
        );
    }

    /**
     * El corazón del servicio: valida, aplica, registra y avisa.
     *
     * @param  Closure(SolicitudRetiro): void|null  $cambios  ajustes propios de cada acción
     */
    private function transicionar(
        SolicitudRetiro $solicitud,
        EstadoSolicitud $nuevo,
        User $actor,
        ?Closure $cambios = null,
        ?string $comentario = null,
    ): SolicitudRetiro {
        [$actualizada, $anterior] = DB::transaction(function () use ($solicitud, $nuevo, $actor, $cambios, $comentario) {
            // Se relee la fila bloqueándola: así se valida contra el estado real
            // de la base y no contra una copia vieja que tenga el controlador.
            $s = SolicitudRetiro::query()->whereKey($solicitud->getKey())->lockForUpdate()->firstOrFail();
            $anterior = $s->estado;

            if (! $anterior->puedePasarA($nuevo)) {
                throw TransicionInvalidaException::entre($anterior, $nuevo);
            }

            if ($cambios) {
                $cambios($s);
            }

            $s->estado = $nuevo;
            $s->save();

            // T32: cada cambio queda registrado, en la misma transacción.
            $s->historial()->create([
                'user_id' => $actor->id,
                'estado_anterior' => $anterior->value,
                'estado_nuevo' => $nuevo->value,
                'comentario' => $comentario,
            ]);

            return [$s, $anterior];
        });

        // Fuera de la transacción: el aviso sale solo si el cambio se guardó.
        SolicitudCambioDeEstado::dispatch($actualizada, $anterior, $nuevo, $actor);

        // El objeto que pasó el controlador queda al día.
        $solicitud->setRawAttributes($actualizada->getAttributes(), true);

        return $actualizada;
    }

    private function cambiarEstadoDeMateriales(SolicitudRetiro $solicitud, string $estado): void
    {
        $ids = $solicitud->materiales()->pluck('materiales.id');

        if ($ids->isNotEmpty()) {
            Material::query()->whereKey($ids)->update(['estado' => $estado]);
        }
    }
}
