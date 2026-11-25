<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\View\View;

/**
 * Página de inicio del panel de administración (T18 / HU13 / RF14).
 */
class PanelController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.panel', [
            'totalUsuarios' => User::count(),
            'usuariosActivos' => User::where('activo', true)->count(),
            'usuariosPorRol' => Rol::withCount('users')->orderBy('id')->get(),
        ]);
    }
}
