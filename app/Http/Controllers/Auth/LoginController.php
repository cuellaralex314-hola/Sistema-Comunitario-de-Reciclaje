<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Inicio y cierre de sesión (T04 / HU2 / RF02).
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Inicia la sesión y vuelve a la página que el usuario quería visitar.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Nuevo identificador de sesión: evita la fijación de sesión.
        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('status', 'Hola, '.$request->user()->name.'. Iniciaste sesión.');
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalida la sesión y renueva el token CSRF: la sesión anterior
        // no se puede volver a usar.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Cerraste sesión.');
    }
}
