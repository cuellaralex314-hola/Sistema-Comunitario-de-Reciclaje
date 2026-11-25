<?php

namespace App\Exceptions;

use App\Enums\EstadoSolicitud;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

/**
 * Se lanza cuando se intenta un cambio de estado que las reglas no permiten
 * (por ejemplo, completar una solicitud pendiente o cancelar una en proceso).
 *
 * Laravel llama a render() solo: el usuario vuelve a la página anterior con
 * el mensaje en $errors->first('solicitud'), sin try/catch en los controladores.
 */
class TransicionInvalidaException extends RuntimeException
{
    public static function entre(EstadoSolicitud $actual, EstadoSolicitud $nuevo): self
    {
        return new self(sprintf(
            'No se puede pasar una solicitud de «%s» a «%s».',
            $actual->etiqueta(),
            $nuevo->etiqueta(),
        ));
    }

    public function render(): RedirectResponse
    {
        return back()->withErrors(['solicitud' => $this->getMessage()]);
    }
}
