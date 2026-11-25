<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Models\Barrio;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Registro de vecinos (T03 / HU1 / RF01).
 */
class RegistroController extends Controller
{
    /**
     * Muestra el formulario de registro con la lista de barrios.
     */
    public function create(): View
    {
        return view('auth.register', [
            'barrios' => Barrio::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Crea la cuenta, inicia la sesión y redirige al inicio.
     */
    public function store(RegistroRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // Los campos se asignan uno por uno: así se ignora cualquier dato extra
        // que llegue en la petición (por ejemplo, alguien que agregue rol_id=1
        // a mano para registrarse como administrador). Además, rol_id no es
        // asignable en masa: se fija aparte (T35).
        $user = new User([
            'name' => $datos['name'],
            'apellido' => $datos['apellido'],
            'barrio_id' => $datos['barrio_id'],
            'email' => $datos['email'],
            'password' => $datos['password'], // el cast 'hashed' del modelo la cifra
        ]);
        $user->rol_id = Rol::VECINO;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('status', "¡Bienvenido/a, {$user->name}! Tu cuenta fue creada.");
    }
}
