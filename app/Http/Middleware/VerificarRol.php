<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo a los usuarios con alguno de los roles indicados (T05 / HU3 / RF03).
 *
 * Uso en las rutas:  ->middleware(['auth', 'rol:admin'])
 *                    ->middleware(['auth', 'rol:admin,recolector'])
 * Va siempre después de 'auth': así un invitado es enviado al login
 * y un usuario sin el rol recibe el error 403.
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $rolDelUsuario = $request->user()?->rol?->nombre;

        if (! in_array($rolDelUsuario, $roles, true)) {
            abort(403, 'No tenés permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
