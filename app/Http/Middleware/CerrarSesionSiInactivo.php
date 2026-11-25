<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si un administrador desactiva una cuenta que tiene la sesión abierta,
 * esa sesión se cierra en el siguiente clic (T17 / baja lógica).
 */
class CerrarSesionSiInactivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Comunicate con el administrador.']);
        }

        return $next($request);
    }
}
