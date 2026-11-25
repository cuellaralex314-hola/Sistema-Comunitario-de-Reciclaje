<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Valida el formulario de registro de vecinos (HU1 / RF01).
 */
class RegistroRequest extends FormRequest
{
    /**
     * Cualquier visitante puede registrarse.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza los datos antes de validarlos: sin espacios sobrantes
     * y el email en minúsculas, para que "Laura@Mail.com" y
     * "laura@mail.com" se detecten como la misma cuenta.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'apellido' => trim((string) $this->input('apellido')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'apellido' => ['required', 'string', 'max:80'],
            'barrio_id' => ['required', 'integer', 'exists:barrios,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'max' => 'El campo :attribute no puede superar los :max caracteres.',
            'email.email' => 'Ingresá un correo electrónico válido.',
            'email.unique' => 'El email ya está registrado.',
            'barrio_id.exists' => 'Elegí un barrio de la lista.',
            'barrio_id.integer' => 'Elegí un barrio de la lista.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'apellido' => 'apellido',
            'barrio_id' => 'barrio',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
