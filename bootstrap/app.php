<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Control de acceso por rol (T05): ->middleware(['auth', 'rol:admin'])
        $middleware->alias([
            'rol' => \App\Http\Middleware\VerificarRol::class,
        ]);

        // Despliegue (T21): Railway atiende HTTPS en su proxy y le pasa la
        // petición a Laravel. Confiar en ese proxy hace que Laravel sepa que la
        // conexión original era HTTPS y genere enlaces y formularios https://.
        $middleware->trustProxies(at: '*');

        // Cuentas desactivadas (T17): se cierra su sesión en el siguiente clic.
        $middleware->web(append: [
            \App\Http\Middleware\CerrarSesionSiInactivo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
