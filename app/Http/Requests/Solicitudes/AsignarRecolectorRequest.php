<?php

namespace App\Http\Requests\Solicitudes;

use App\Models\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignarRecolectorRequest extends FormRequest
{
    /** El permiso lo controlan la ruta (rol:admin) y la policy. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recolector_id' => [
                'required',
                'integer',
                // Tiene que existir, ser recolector y estar activo.
                Rule::exists('users', 'id')
                    ->where('rol_id', Rol::RECOLECTOR)
                    ->where('activo', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recolector_id.required' => 'Elegí un recolector.',
            'recolector_id.integer' => 'Elegí un recolector de la lista.',
            'recolector_id.exists' => 'Elegí un recolector activo de la lista.',
        ];
    }
}
