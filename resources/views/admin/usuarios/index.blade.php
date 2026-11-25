@extends('layouts.app')

@section('title', 'Usuarios y roles')

@section('content')
<div class="bg-white shadow-sm sm:rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Usuarios y roles</h1>
    <p class="text-gray-600 mb-4">Cambiá el rol de cada usuario o desactivá su cuenta. Las cuentas desactivadas no pueden iniciar sesión, pero se conserva su historial.</p>

    @include('admin.partials.menu')

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ $errors->first() }}</div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex flex-wrap gap-3 items-end mb-6">
        <div class="flex-1 min-w-[200px]">
            <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="search" id="buscar" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre, apellido o email"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>
        <div>
            <label for="rol" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select id="rol" name="rol" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Todos</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id }}" @selected(($filtros['rol'] ?? null) == $rol->id)>{{ ucfirst($rol->nombre) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select id="estado" name="estado" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Todos</option>
                <option value="activos" @selected(($filtros['estado'] ?? null) === 'activos')>Activos</option>
                <option value="inactivos" @selected(($filtros['estado'] ?? null) === 'inactivos')>Dados de baja</option>
            </select>
        </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md">Filtrar</button>
        @if (array_filter($filtros))
            <a href="{{ route('admin.usuarios.index') }}" class="py-2 text-sm text-gray-600 hover:underline">Limpiar</a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-200">
                    <th class="py-2 pr-4 font-medium">Usuario</th>
                    <th class="py-2 pr-4 font-medium">Barrio</th>
                    <th class="py-2 pr-4 font-medium">Rol</th>
                    <th class="py-2 pr-4 font-medium">Estado</th>
                    <th class="py-2 font-medium">Cuenta</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($usuarios as $usuario)
                    @php($esYo = $usuario->is(auth()->user()))
                    <tr>
                        <td class="py-3 pr-4">
                            <p class="font-medium text-gray-900">{{ $usuario->name }} {{ $usuario->apellido }} @if ($esYo)<span class="text-xs text-gray-500">(vos)</span>@endif</p>
                            <p class="text-gray-500">{{ $usuario->email }}</p>
                        </td>
                        <td class="py-3 pr-4 text-gray-700">{{ $usuario->barrio?->nombre ?? '—' }}</td>
                        <td class="py-3 pr-4">
                            @if ($esYo)
                                <span class="capitalize">{{ $usuario->rol->nombre }}</span>
                            @else
                                <form method="POST" action="{{ route('admin.usuarios.rol', $usuario) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label for="rol-{{ $usuario->id }}" class="sr-only">Rol de {{ $usuario->name }}</label>
                                    <select id="rol-{{ $usuario->id }}" name="rol_id" class="px-2 py-1 border border-gray-300 rounded-md">
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->id }}" @selected($usuario->rol_id === $rol->id)>{{ ucfirst($rol->nombre) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-1 rounded-md border border-green-600 text-green-700 hover:bg-green-50">Guardar</button>
                                </form>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            @if ($usuario->activo)
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-gray-200 text-gray-700 text-xs font-semibold">Dado de baja</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @unless ($esYo)
                                <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from(($usuario->activo ? '¿Desactivar' : '¿Reactivar').' la cuenta de '.$usuario->name.' '.$usuario->apellido.'?') }});">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        @class([
                                            'px-3 py-1 rounded-md text-sm font-medium',
                                            'text-red-700 border border-red-300 hover:bg-red-50' => $usuario->activo,
                                            'text-green-700 border border-green-300 hover:bg-green-50' => ! $usuario->activo,
                                        ])>
                                        {{ $usuario->activo ? 'Desactivar' : 'Reactivar' }}
                                    </button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">No hay usuarios que coincidan con la búsqueda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
