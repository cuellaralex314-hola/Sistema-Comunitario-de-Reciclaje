<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActualizarRolRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Administración de usuarios y roles (T17 / HU3 / HU13 / RF03 / RF14).
 * Solo para el administrador: las rutas usan 'rol:admin' y 'can:usuarios.gestionar'.
 */
class UsuarioController extends Controller
{
    /**
     * Listado con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'rol' => ['nullable', 'integer', 'exists:roles,id'],
            'estado' => ['nullable', 'in:activos,inactivos'],
        ]);

        $usuarios = User::query()
            ->with(['rol', 'barrio'])
            ->when($filtros['buscar'] ?? null, function ($consulta, $texto) {
                $consulta->where(function ($q) use ($texto) {
                    $q->where('name', 'like', "%{$texto}%")
                        ->orWhere('apellido', 'like', "%{$texto}%")
                        ->orWhere('email', 'like', "%{$texto}%");
                });
            })
            ->when($filtros['rol'] ?? null, fn ($consulta, $rol) => $consulta->where('rol_id', $rol))
            ->when(($filtros['estado'] ?? null) === 'activos', fn ($consulta) => $consulta->where('activo', true))
            ->when(($filtros['estado'] ?? null) === 'inactivos', fn ($consulta) => $consulta->where('activo', false))
            ->orderBy('name')
            ->orderBy('apellido')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Rol::orderBy('id')->get(),
            'filtros' => $filtros,
        ]);
    }

    /**
     * Cambia el rol de un usuario.
     */
    public function actualizarRol(ActualizarRolRequest $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No podés cambiar tu propio rol.');
        }

        // rol_id no es asignable en masa (T35): se asigna explícitamente.
        $usuario->rol_id = $request->validated('rol_id');
        $usuario->save();

        return back()->with('status', "Rol de {$usuario->name} {$usuario->apellido} actualizado a {$usuario->fresh()->rol->nombre}.");
    }

    /**
     * Desactiva o reactiva una cuenta (baja lógica).
     */
    public function cambiarEstado(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No podés desactivar tu propia cuenta.');
        }

        $usuario->activo = ! $usuario->activo;
        $usuario->save();

        $accion = $usuario->activo ? 'reactivada' : 'desactivada';

        return back()->with('status', "Cuenta de {$usuario->name} {$usuario->apellido} {$accion}.");
    }
}
