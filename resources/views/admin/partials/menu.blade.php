{{-- Menú de secciones del panel admin (T18). Cada sección aparece solo si el rol tiene el permiso. --}}
@php
    $secciones = [
        ['ruta' => 'admin.panel', 'activa' => 'admin.panel', 'texto' => 'Resumen', 'permiso' => null],
        ['ruta' => 'admin.usuarios.index', 'activa' => 'admin.usuarios.*', 'texto' => 'Usuarios y roles', 'permiso' => 'usuarios.gestionar'],
    ];
@endphp
<nav aria-label="Secciones del panel" class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-3">
    @foreach ($secciones as $seccion)
        @if (is_null($seccion['permiso']) || auth()->user()->can($seccion['permiso']))
            @if (Route::has($seccion['ruta']))
                <a href="{{ route($seccion['ruta']) }}"
                   @class([
                       'px-3 py-2 rounded-md text-sm font-medium',
                       'bg-green-600 text-white' => request()->routeIs($seccion['activa']),
                       'text-gray-600 hover:bg-gray-100' => ! request()->routeIs($seccion['activa']),
                   ])>
                    {{ $seccion['texto'] }}
                </a>
            @endif
        @endif
    @endforeach
    <span class="px-3 py-2 text-sm text-gray-400">Puntos, solicitudes y estadísticas: próximamente</span>
</nav>
