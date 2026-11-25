<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Permisos como Gates (T05): cada clave de la tabla `permisos`
        // (por ejemplo 'usuarios.gestionar') se puede consultar con
        // Gate::allows(), @can en las vistas o ->middleware('can:...').
        // Si el rol del usuario no tiene el permiso, se devuelve null y
        // Laravel sigue con las Policies (por ejemplo, la de materiales).
        Gate::before(function (User $user, string $ability) {
            return $user->tienePermiso($ability) ? true : null;
        });

        // Despliegue (T21): en producción todos los enlaces usan https://.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
