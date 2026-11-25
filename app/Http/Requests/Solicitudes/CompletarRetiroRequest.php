<?php

namespace App\Http\Requests\Solicitudes;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Pesos reales del retiro: pesos[<id del material>] = kg.
 * Que estén TODOS los materiales de la solicitud lo verifica el servicio.
 */
class CompletarRetiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pesos' => ['required', 'array', 'min:1'],
            'pesos.*' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'pesos.required' => 'Cargá el peso de los materiales retirados.',
            'pesos.*.required' => 'Cargá el peso de cada material.',
            'pesos.*.numeric' => 'El peso tiene que ser un número (por ejemplo 3.5).',
            'pesos.*.min' => 'El peso no puede ser negativo.',
            'pesos.*.max' => 'El peso es demasiado grande.',
        ];
    }
}
