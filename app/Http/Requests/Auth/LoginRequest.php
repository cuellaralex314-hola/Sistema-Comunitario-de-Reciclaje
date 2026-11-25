<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Valida el inicio de sesión y lo protege contra intentos repetidos (T04 / HU2 / RF02).
 */
class LoginRequest extends FormRequest
{
    /** Intentos fallidos permitidos antes de bloquear. */
    public const MAX_INTENTOS = 5;

    /** Segundos de bloqueo después de superar el límite. */
    public const SEGUNDOS_BLOQUEO = 60;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Ingresá tu correo electrónico.',
            'email.email' => 'Ingresá un correo electrónico válido.',
            'password.required' => 'Ingresá tu contraseña.',
        ];
    }

    /**
     * Intenta iniciar la sesión con el email y la contraseña.
     *
     * @throws ValidationException si los datos no coinciden, la cuenta está
     *                             desactivada o se superó el límite de intentos.
     */
    public function authenticate(): void
    {
        $this->asegurarQueNoEsteBloqueado();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->claveDeBloqueo(), self::SEGUNDOS_BLOQUEO);

            // El mismo mensaje para email inexistente y contraseña incorrecta:
            // así no se revela qué emails están registrados.
            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña son incorrectos.',
            ]);
        }

        // Baja lógica: una cuenta desactivada no puede entrar.
        if (! Auth::user()->activo) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está desactivada. Comunicate con el administrador.',
            ]);
        }

        RateLimiter::clear($this->claveDeBloqueo());
    }

    /**
     * Corta el intento si ya se superó el límite.
     */
    public function asegurarQueNoEsteBloqueado(): void
    {
        if (! RateLimiter::tooManyAttempts($this->claveDeBloqueo(), self::MAX_INTENTOS)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->claveDeBloqueo());

        throw ValidationException::withMessages([
            'email' => "Demasiados intentos fallidos. Probá de nuevo en {$segundos} segundos.",
        ]);
    }

    /**
     * El bloqueo se cuenta por email + IP: frena a quien prueba contraseñas
     * sin bloquear a otros vecinos que usan la misma red.
     */
    public function claveDeBloqueo(): string
    {
        return Str::transliterate($this->input('email').'|'.$this->ip());
    }
}
