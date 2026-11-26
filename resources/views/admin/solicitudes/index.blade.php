@extends('layouts.app')

@section('title', 'Administración de Solicitudes de Retiro — BarrioVerde')

@section('content')
@php
    $solicitudesAdmin = $solicitudes ?? collect([
        (object) [
            'id' => 104,
            'codigo' => 'SOL-0104',
            'fecha' => '21/11/2026',
            'vecino' => 'Martín Sosa',
            'telefono' => '3704-458921',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'materiales' => ['Plástico (PET / envases)', 'Papel y cartón', 'Vidrio'],
            'peso_aproximado' => 'Mediano (5 a 15 kg)',
            'franja_horaria' => 'Mañana (08:00 a 12:00)',
            'estado' => 'pendiente',
            'recolector_id' => null,
        ],
        (object) [
            'id' => 103,
            'codigo' => 'SOL-0103',
            'fecha' => '21/11/2026',
            'vecino' => 'Sofía Benítez',
            'telefono' => '3704-512309',
            'barrio' => 'Barrio Virgen del Rosario',
            'direccion' => 'Juan José Silva 410',
            'materiales' => ['Papel y cartón', 'Metales / latas'],
            'peso_aproximado' => 'Liviano (hasta 5 kg)',
            'franja_horaria' => 'Tarde (13:00 a 17:00)',
            'estado' => 'pendiente',
            'recolector_id' => null,
        ],
        (object) [
            'id' => 101,
            'codigo' => 'SOL-0101',
            'fecha' => '18/11/2026',
            'vecino' => 'Martín Sosa',
            'telefono' => '3704-458921',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'materiales' => ['Electrónicos pequeños'],
            'peso_aproximado' => 'Liviano (hasta 5 kg)',
            'franja_horaria' => 'Tarde (13:00 a 17:00)',
            'estado' => 'asignada',
            'recolector_id' => 1,
        ],
        (object) [
            'id' => 96,
            'codigo' => 'SOL-0096',
            'fecha' => '12/11/2026',
            'vecino' => 'Valeria Giménez',
            'telefono' => '3704-609112',
            'barrio' => 'Centro',
            'direccion' => 'Rivadavia 620',
            'materiales' => ['Papel y cartón', 'Vidrio'],
            'peso_aproximado' => 'Grande (más de 15 kg)',
            'franja_horaria' => 'Mañana (08:00 a 12:00)',
            'estado' => 'completada',
            'recolector_id' => 2,
        ],
    ]);

    $recolectores = $recolectores ?? collect([
        (object) ['id' => 1, 'nombre' => 'Carlos Gómez'],
        (object) ['id' => 2, 'nombre' => 'Lucía Fernández'],
        (object) ['id' => 3, 'nombre' => 'Diego Acosta'],
    ]);

    $estadoFiltro = request('estado', 'todas');
    $buscar = trim((string) request('q', ''));

    $filtradas = $solicitudesAdmin->filter(function ($item) use ($estadoFiltro, $buscar) {
        if ($estadoFiltro !== 'todas' && $item->estado !== $estadoFiltro) {
            return false;
        }
        if ($buscar !== '') {
            $haystack = strtolower(
                ($item->codigo ?? '') . ' ' .
                ($item->vecino ?? '') . ' ' .
                ($item->barrio ?? '') . ' ' .
                ($item->direccion ?? '')
            );
            return str_contains($haystack, strtolower($buscar));
        }
        return true;
    });

    $estilosEstado = [
        'pendiente'  => ['label' => 'Pendiente',  'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'asignada'   => ['label' => 'Asignada',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'en_camino'  => ['label' => 'En camino',  'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'completada' => ['label' => 'Completada', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'cancelada'  => ['label' => 'Cancelada',  'badge' => 'bg-gray-100 text-gray-600 border-gray-200'],
    ];
@endphp

<div class="mx-auto max-w-7xl px-4 py-8">
    {{-- Encabezado --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                Administración
            </span>
            <h1 class="mt-2 text-2xl font-bold text-gray-800">Gestión de Solicitudes de Retiro</h1>
            <p class="text-sm text-gray-500">
                Revisá las solicitudes entrantes, asigná recolectores y actualizá el estado de cada retiro.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('solicitudes.create') }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                + Nueva solicitud
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Resumen rápido --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Total visibles</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $filtradas->count() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-amber-700">Pendientes</p>
            <p class="mt-1 text-2xl font-bold text-amber-700">
                {{ $solicitudesAdmin->where('estado', 'pendiente')->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-blue-700">Asignadas / En camino</p>
            <p class="mt-1 text-2xl font-bold text-blue-700">
                {{ $solicitudesAdmin->whereIn('estado', ['asignada', 'en_camino'])->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-emerald-700">Completadas</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700">
                {{ $solicitudesAdmin->where('estado', 'completada')->count() }}
            </p>
        </div>
    </div>

    {{-- Filtros y búsqueda --}}
    <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-1.5">
            @php
                $estados = [
                    'todas'      => 'Todas',
                    'pendiente'  => 'Pendientes',
                    'asignada'   => 'Asignadas',
                    'en_camino'  => 'En camino',
                    'completada' => 'Completadas',
                    'cancelada'  => 'Canceladas',
                ];
            @endphp

            @foreach ($estados as $key => $label)
                <button
                    type="submit"
                    name="estado"
                    value="{{ $key }}"
                    class="rounded-full border px-3 py-1.5 text-xs font-medium transition {{ $estadoFiltro === $key
                        ? 'border-emerald-600 bg-emerald-600 text-white'
                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-emerald-300 hover:text-emerald-700' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex w-full items-center gap-2 sm:w-auto">
            <input
                type="text"
                name="q"
                value="{{ $buscar }}"
                placeholder="Buscar código, vecino, barrio o calle..."
                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:w-64"
            />
            <button
                type="submit"
                class="rounded-lg bg-gray-800 px-3.5 py-2 text-xs font-semibold text-white hover:bg-gray-900"
            >
                Buscar
            </button>
            @if ($buscar !== '' || $estadoFiltro !== 'todas')
                <a
                    href="{{ route('admin.solicitudes.index') }}"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50"
                >
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    {{-- Tabla administrativa --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Solicitud</th>
                        <th class="px-4 py-3">Vecino y dirección</th>
                        <th class="px-4 py-3">Materiales y turno</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Recolector / Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($filtradas as $solicitud)
                        @php
                            $st = $estilosEstado[$solicitud->estado] ?? $estilosEstado['pendiente'];
                        @endphp
                        <tr class="align-top hover:bg-gray-50/70">
                            <td class="whitespace-nowrap px-4 py-3.5">
                                <p class="font-mono text-xs font-bold text-gray-800">
                                    {{ $solicitud->codigo ?? 'SOL-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-400">{{ $solicitud->fecha }}</p>
                            </td>

                            <td class="px-4 py-3.5">
                                <p class="font-medium text-gray-800">{{ $solicitud->vecino }}</p>
                                <p class="text-xs text-gray-600">{{ $solicitud->direccion }}</p>
                                <p class="text-xs text-gray-400">{{ $solicitud->barrio }} · {{ $solicitud->telefono }}</p>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ((array) $solicitud->materiales as $mat)
                                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                            {{ $mat }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="mt-1.5 text-xs text-gray-500">
                                    {{ $solicitud->peso_aproximado }} · {{ $solicitud->franja_horaria }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $st['badge'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <form
                                    action="{{ route('admin.solicitudes.asignar', $solicitud->id) }}"
                                    method="POST"
                                    class="flex flex-wrap items-center gap-2"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="recolector_id"
                                        aria-label="Seleccionar recolector"
                                        class="rounded-lg border-gray-300 py-1.5 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    >
                                        <option value="">Sin asignar</option>
                                        @foreach ($recolectores as $rec)
                                            <option value="{{ $rec->id }}" @selected(($solicitud->recolector_id ?? null) == $rec->id)>
                                                {{ $rec->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select
                                        name="estado"
                                        aria-label="Cambiar estado"
                                        class="rounded-lg border-gray-300 py-1.5 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    >
                                        @foreach ($estados as $key => $label)
                                            @if ($key !== 'todas')
                                                <option value="{{ $key }}" @selected($solicitud->estado === $key)>
                                                    {{ $label }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700"
                                    >
                                        Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                No se encontraron solicitudes con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection