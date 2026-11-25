<?php

namespace App\Enums;

/**
 * Estados de una solicitud de retiro y las transiciones permitidas (T31).
 *
 *   pendiente ──asignar──▶ en_proceso ──completar──▶ completada
 *       │                      │
 *       ├──cancelar──▶ cancelada
 *       └──rechazar──▶ rechazada ◀──rechazar──┘
 *
 * completada, cancelada y rechazada son finales: no salen a ningún lado.
 */
enum EstadoSolicitud: string
{
    case Pendiente = 'pendiente';
    case EnProceso = 'en_proceso';
    case Completada = 'completada';
    case Cancelada = 'cancelada';
    case Rechazada = 'rechazada';

    /**
     * A qué estados se puede pasar desde este.
     *
     * @return list<self>
     */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::Pendiente => [self::EnProceso, self::Cancelada, self::Rechazada],
            self::EnProceso => [self::Completada, self::Rechazada],
            self::Completada, self::Cancelada, self::Rechazada => [],
        };
    }

    public function puedePasarA(self $nuevo): bool
    {
        return in_array($nuevo, $this->transicionesPermitidas(), true);
    }

    public function esFinal(): bool
    {
        return $this->transicionesPermitidas() === [];
    }

    /** Texto para mostrar en las vistas. */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Completada => 'Completada',
            self::Cancelada => 'Cancelada',
            self::Rechazada => 'Rechazada',
        };
    }
}
