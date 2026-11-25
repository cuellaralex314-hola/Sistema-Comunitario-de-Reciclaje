<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el cambio de rol de un usuario (T17 / HU3).
 */
class ActualizarRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('usuarios.gestionar');
    }

    public function rules(): array
    {
        return [
            'rol_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.required' => 'Elegí un rol.',
            'rol_id.integer' => 'Elegí un rol de la lista.',
            'rol_id.exists' => 'Elegí un rol de la lista.',
        ];
    }
}
