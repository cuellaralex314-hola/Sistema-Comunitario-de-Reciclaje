@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
<div class="bg-white shadow-sm sm:rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Panel de administración</h1>
    <p class="text-gray-600 mb-4">Solo el administrador puede ver esta sección.</p>

    @include('admin.partials.menu')

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500">Usuarios registrados</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalUsuarios }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500">Usuarios activos</p>
            <p class="text-3xl font-bold text-green-700">{{ $usuariosActivos }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500">Dados de baja</p>
            <p class="text-3xl font-bold text-gray-500">{{ $totalUsuarios - $usuariosActivos }}</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-2">Usuarios por rol</h2>
    <ul class="divide-y divide-gray-100 border border-gray-200 rounded-lg mb-6">
        @foreach ($usuariosPorRol as $rol)
            <li class="flex justify-between px-4 py-2">
                <span class="capitalize">{{ $rol->nombre }}</span>
                <span class="font-semibold">{{ $rol->users_count }}</span>
            </li>
        @endforeach
    </ul>

    {{-- Las secciones se muestran según los permisos del rol (Gates de T05). --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Secciones</h2>
    <ul class="space-y-2 text-gray-700">
        @can('usuarios.gestionar')
            @if (Route::has('admin.usuarios.index'))
                <li><a href="{{ route('admin.usuarios.index') }}" class="text-green-700 hover:underline">Usuarios y roles</a></li>
            @endif
        @endcan
        @can('puntos.gestionar')
            <li>Puntos de recepción <span class="text-sm text-gray-500">(T24, Fabián)</span></li>
        @endcan
        @can('solicitudes.gestionar')
            <li>Solicitudes de retiro <span class="text-sm text-gray-500">(T43)</span></li>
        @endcan
        @can('estadisticas.ver')
            <li>Estadísticas <span class="text-sm text-gray-500">(T58)</span></li>
        @endcan
    </ul>
</div>
@endsection
