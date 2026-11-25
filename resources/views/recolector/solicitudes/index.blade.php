@extends('layouts.app')

@section('title', 'Mis Retiros Asignados — BarrioVerde')

@section('content')
@php
    $retirosAsignados = $retiros ?? collect([
        (object) [
            'id' => 101,
            'codigo' => 'SOL-0101',
            'fecha' => '18/11/2026',
            'vecino' => 'Martín Sosa',
            'telefono' => '3704-458921',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'materiales' => ['Electrónicos pequeños', 'Metales / latas'],
            'peso_aproximado' => 'Liviano (hasta 5 kg)',
            'franja_horaria' => 'Tarde (13:00 a 17:00)',
            'estado' => 'asignada',
            'observaciones' => 'Tocar timbre negro.',
        ],
        (object) [
            'id' => 105,
            'codigo' => 'SOL-0105',
            'fecha' => '22/11/2026',
            'vecino' => 'Luciana Maidana',
            'telefono' => '3704-778210',
            'barrio' => 'Barrio La Floresta',
            'direccion' => 'España 1120',
            'materiales' => ['Plástico (PET / envases)', 'Papel y cartón'],
            'peso_aproximado' => 'Mediano (5 a 15 kg)',
            'franja_horaria' => 'Mañana (08:00 a 12:00)',
            'estado' => 'en_camino',
            'observaciones' => 'Bolsas en la vereda junto al cantero.',
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
            'observaciones' => 'Cajas plegadas y atadas.',
        ],
    ]);

    $estadoFiltro = request('estado', 'activos');

    $retirosFiltrados = $retirosAsignados->filter(function ($item) use ($estadoFiltro) {
        return match ($estadoFiltro) {
            'activos'    => in_array($item->estado, ['asignada', 'en_camino']),
            'asignada'   => $item->estado === 'asignada',
            'en_camino'  => $item->estado === 'en_camino',
            'completada' => $item->estado === 'completada',
            default      => true,
        };
    });

    $estilosEstado = [
        'asignada'   => ['label' => 'Asignada',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'en_camino'  => ['label' => 'En camino',  'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'completada' => ['label' => 'Completada', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
    ];
@endphp

<div class="mx-auto max-w-5xl px-4 py-8">
    {{-- Encabezado --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                Solicitudes y retiros
            </span>
            <h1 class="mt-2 text-2xl font-bold text-gray-800">Pantalla del Recolector</h1>
            <p class="text-sm text-gray-500">
                Consultá tus retiros asignados, revisá direcciones y actualizá el avance en recorrido.
            </p>
        </div>

        <a
            href="{{ route('admin.solicitudes.index') }}"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Ir a administración
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Métricas rápidas del recolector --}}
    <div class="mb-6 grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-blue-700">Asignados</p>
            <p class="mt-1 text-2xl font-bold text-blue-700">
                {{ $retirosAsignados->where('estado', 'asignada')->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-indigo-700">En camino</p>
            <p class="mt-1 text-2xl font-bold text-indigo-700">
                {{ $retirosAsignados->where('estado', 'en_camino')->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-emerald-700">Completados</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700">
                {{ $retirosAsignados->where('estado', 'completada')->count() }}
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @php
            $filtros = [
                'activos'    => 'En curso',
                'asignada'   => 'Asignados',
                'en_camino'  => 'En camino',
                'completada' => 'Completados',
                'todos'      => 'Todos',
            ];
        @endphp

        @foreach ($filtros as $clave => $texto)
            <a
                href="{{ route('recolector.solicitudes.index', ['estado' => $clave]) }}"
                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition {{ $estadoFiltro === $clave
                    ? 'border-emerald-600 bg-emerald-600 text-white'
                    : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-300 hover:text-emerald-700' }}"
            >
                {{ $texto }}
            </a>
        @endforeach
    </div>

    {{-- Tarjetas de retiros --}}
    @if ($retirosFiltrados->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center">
            <h2 class="text-base font-semibold text-gray-800">No hay retiros en este filtro</h2>
            <p class="mt-1 text-sm text-gray-500">
                Probá cambiar el filtro para ver asignaciones pendientes o completadas.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($retirosFiltrados as $retiro)
                @php
                    $st = $estilosEstado[$retiro->estado] ?? $estilosEstado['asignada'];
                @endphp

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-sm font-bold text-gray-800">
                                    {{ $retiro->codigo ?? 'SOL-' . str_pad($retiro->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $st['badge'] }}">
                                    {{ $st['label'] }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $retiro->franja_horaria }}
                                </span>
                            </div>

                            <p class="text-base font-semibold text-gray-800">
                                {{ $retiro->direccion }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $retiro->barrio }} · Vecino: <span class="font-medium text-gray-700">{{ $retiro->vecino }}</span>
                                @if (!empty($retiro->telefono))
                                    · Tel: {{ $retiro->telefono }}
                                @endif
                            </p>
                        </div>

                        <div class="text-right text-xs text-gray-500">
                            <p class="font-medium text-gray-700">{{ $retiro->peso_aproximado }}</p>
                            <p class="mt-0.5">Fecha: {{ $retiro->fecha }}</p>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ((array) $retiro->materiales as $mat)
                            <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                {{ $mat }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-500">
                            <span class="font-medium text-gray-700">Indicaciones:</span>
                            {{ $retiro->observaciones ?: 'Sin observaciones adicionales.' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($retiro->estado === 'asignada')
                                <form action="{{ route('recolector.solicitudes.estado', $retiro->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="en_camino">
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                                    >
                                        Iniciar traslado
                                    </button>
                                </form>
                            @endif

                            @if (in_array($retiro->estado, ['asignada', 'en_camino']))
                                <form action="{{ route('recolector.solicitudes.estado', $retiro->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="completada">
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700"
                                    >
                                        Marcar completado
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection